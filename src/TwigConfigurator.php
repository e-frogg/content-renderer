<?php


namespace Efrogg\ContentRenderer;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\Core\ConfiguratorInterface;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use Efrogg\ContentRenderer\Event\TwigConfigurationEvent;
use LogicException;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Extension\EscaperExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigConfigurator implements ConfiguratorInterface
{

    public const PRIORITY_HIGH=0;
    public const PRIORITY_NORMAL=10;
    public const PRIORITY_LOW=20;
    protected EventDispatcherInterface $eventDispatcher;
    protected FileLocator $fileLocator;
    /**
     * @var CmsRenderer
     */
    private $cmsRenderer;

    /**
     * @var AssetResolver
     */
    private $assetResolver;
    /**
     * @var Environment
     */
    private $environment;

    /**
     * TwigRenderer constructor.
     *
     * @param CmsRenderer    $cmsRenderer
     * @param  AssetResolver $assetResolver
     * @param  Environment   $environment
     */
    public function __construct(CmsRenderer $cmsRenderer,AssetResolver $assetResolver,Environment $environment, EventDispatcherInterface $eventDispatcher, FileLocator $fileLocator)
    {
        $this->cmsRenderer = $cmsRenderer;
        $this->assetResolver = $assetResolver;
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @throws LogicException
     */
    public function configure(): void
    {
        $this->environment->addFilter(new TwigFilter('cms', [$this->cmsRenderer, 'convertAndRender'], ['is_safe' => ['html']]));
        $this->environment->addFilter(new TwigFilter('cmsZone', [$this->cmsRenderer, 'convertAndRenderMultiple'], ['is_safe' => ['html']]));
        $this->environment->addFunction(new TwigFunction('cmsNode', [$this->cmsRenderer, 'renderNodeById'], ['is_safe' => ['html']]));

        $this->environment->addFilter(new TwigFilter('cmsImage', [$this, 'renderImageSrc'], ['is_safe' => ['html']]));
        $this->environment->addFunction(new TwigFunction('cmsImage', [$this, 'renderImage'], ['is_safe' => ['html']]));
        $this->environment->getExtension(EscaperExtension::class)->setEscaper('json_string', [$this, 'jsonStringEscape']);

        // déclenche l'event pour ajouter
        $loader = $this->environment->getLoader();
        if ($loader instanceof ChainLoader) {
            $pathCollector = new TwigPathCollector();
            $this->eventDispatcher->dispatch(new TwigConfigurationEvent($this->environment, $pathCollector));

            $filesystemLoader = new FilesystemLoader();
            $filesystemLoader->setPaths($pathCollector->getSortedPaths(), 'CMS');
            $loader->addLoader($filesystemLoader);
        }
    }

    public function jsonStringEscape(Environment $_twig, ?string $string): ?string
    {
        if (null === $string) {
            return null;
        }
        return str_replace('"', '\\"', $string);
    }

    /**
     * @param         $imageId
     * @param array   $parameters
     *
     * @return string
     * @throws LogicException
     */
    public function renderImageSrc($imageId, $parameters = []): string
    {
        return $this->renderImage($imageId, $parameters)->getSrc() ?? '';
    }

    /**
     * @param         $asset
     * @param  array  $parameters
     * @return Asset
     * @throws LogicException
     */
    public function renderImage($asset, $parameters = []): Asset
    {
        // on cherche un AssetHandler pour
        if (null === $this->assetResolver) {
            throw new LogicException('AssetResolver is not defined. You cannot manage assets');
        }

        // cas d'un tableau d'assets,
        // on ne traite que le premier (ex : image simple sur squidex)
        if(is_array($asset) && isset($asset[0]) && $asset[0] instanceof Asset) {
            $asset = $asset[0];
        }

        try {
            $assetHandler = $this->assetResolver->resolve($asset);
            return $assetHandler->getAsset($asset, $parameters);
        } catch (InvalidSolvableException $e) {
        } catch (SolverNotFoundException $e) {
        }

        // on a fourni un asset, on peut le retourner directement
        if($asset instanceof Asset) {
            return $asset;
        }

        // on a fourni les données d'un Asset (ex : ['src'=>'...'])
        if(is_array($asset)) {
            return new Asset($asset);
        }

        // si une string est fournie, il y a des chances que ce soit le lien de l'asset
        if(is_string($asset)) {
            return new Asset(['src'=>$asset]);
        }

        // on retourne un asset vide sinon, faute de mieux
        return new Asset();
    }

}
