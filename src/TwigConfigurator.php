<?php


namespace Efrogg\ContentRenderer;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\Core\ConfiguratorInterface;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use LogicException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigConfigurator implements ConfiguratorInterface
{

    /**
     * TwigRenderer constructor.
     *
     * @param CmsRenderer   $cmsRenderer
     * @param AssetResolver $assetResolver
     * @param Environment   $environment
     */
    public function __construct(
        private readonly CmsRenderer $cmsRenderer,
        private readonly AssetResolver $assetResolver,
        private readonly Environment $environment,
        private readonly SluggerInterface $slugger
    ) {
    }

    /**
     * @throws LogicException
     */
    public function configure(): void
    {
        $this->environment->addFilter(new TwigFilter('cms', [$this->cmsRenderer, 'convertAndRender'], ['is_safe' => ['html']]));
        $this->environment->addFilter(new TwigFilter('cmsList', [$this->cmsRenderer, 'convertAndRenderList'], ['is_safe' => ['html']]));
        $this->environment->addFunction(new TwigFunction('cmsNode', [$this->cmsRenderer, 'renderNodeById'], ['is_safe' => ['html']]));
        $this->environment->addFilter(new TwigFilter('slug', [$this->slugger, 'slug'], ['is_safe' => ['html']]));

        $this->environment->addFilter(new TwigFilter('cmsImage', [$this, 'renderImageSrc'], ['is_safe' => ['html']]));
        $this->environment->addFunction(new TwigFunction('cmsImage', [$this, 'renderImage'], ['is_safe' => ['html']]));
    }

    /**
     * @param array<mixed> $parameters
     *
     * @throws LogicException
     */
    public function renderImageSrc(mixed $imageId, array $parameters = []): string
    {
        return $this->renderImage($imageId, $parameters)->getSrc() ?? '';
    }

    /**
     * @param mixed        $asset
     * @param array<mixed> $parameters
     *
     * @return Asset
     */
    public function renderImage(mixed $asset, array $parameters = []): Asset
    {
        // cas d'un tableau d'assets,
        // on ne traite que le premier (ex : image simple sur squidex)
        if (is_array($asset) && isset($asset[0]) && $asset[0] instanceof Asset) {
            $asset = $asset[0];
        }

        try {
            return $this->assetResolver->resolve($asset)->getAsset($asset, $parameters);
        } catch (InvalidSolvableException|SolverNotFoundException) {
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
