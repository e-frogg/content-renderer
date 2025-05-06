<?php


namespace Efrogg\ContentRenderer;


use Efrogg\ContentRenderer\Asset\Asset;
use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\Core\Resolver\Exception\InvalidSolvableException;
use Efrogg\ContentRenderer\Core\Resolver\Exception\SolverNotFoundException;
use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigConfiguratorExtension extends AbstractExtension
{
    /**
     * @var CmsRendererInterface
     */
    private $cmsRenderer;

    /**
     * @var AssetResolver
     */
    private $assetResolver;

    /**
     * TwigRenderer constructor.
     *
     * @param CmsRendererInterface $cmsRenderer
     * @param AssetResolver $assetResolver
     */
    public function __construct(
        CmsRendererInterface $cmsRenderer,
        AssetResolver $assetResolver
    ) {
        $this->cmsRenderer = $cmsRenderer;
        $this->assetResolver = $assetResolver;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('cms', $this->cmsRenderer->convertAndRender(...), ['is_safe' => ['html']]),
            new TwigFilter('cmsZone', $this->cmsRenderer->convertAndRenderMultiple(...), ['is_safe' => ['html']]),
            new TwigFilter('cmsImage', $this->renderImageSrc(...), ['is_safe' => ['html']]),

        ];
    }

    public function getTests()
    {
        return [];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cmsNode', $this->cmsRenderer->renderNodeById(...), ['is_safe' => ['html']]),
            new TwigFunction('cmsImage', $this->renderImage(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param         $imageId
     * @param array $parameters
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
     * @param array $parameters
     *
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
        if (is_array($asset) && isset($asset[0]) && $asset[0] instanceof Asset) {
            $asset = $asset[0];
        }

        try {
            $assetHandler = $this->assetResolver->resolve($asset);

            return $assetHandler->getAsset($asset, $parameters);
        } catch (InvalidSolvableException $e) {
        } catch (SolverNotFoundException $e) {
        }

        // on a fourni un asset, on peut le retourner directement
        if ($asset instanceof Asset) {
            return $asset;
        }

        // on a fourni les données d'un Asset (ex : ['src'=>'...'])
        if (is_array($asset)) {
            return new Asset($asset);
        }

        // si une string est fournie, il y a des chances que ce soit le lien de l'asset
        if (is_string($asset)) {
            return new Asset(['src' => $asset]);
        }

        // on retourne un asset vide sinon, faute de mieux
        return new Asset();
    }

}
