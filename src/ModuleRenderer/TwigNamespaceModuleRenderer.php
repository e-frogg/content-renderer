<?php

namespace Efrogg\ContentRenderer\ModuleRenderer;

use Efrogg\ContentRenderer\DependencyInjection\ContentRendererConfig;
use Twig\Environment;

/**
 * @see AbstractTwigModuleRenderer
 * this renderer is an implementation of AbstractTwigModuleRenderer
 * the template resolution is a simple concatenation of the base namespace and the node type
 * the extension is '.twig' by default, and can be changed. ex : .html.twig
 * ex :
 *   base namespace : 'cms/'
 *   node type : 'paragraph'
 *   => template : 'cms/paragraph.twig'
 *
 * Class TwigNamespaceModuleRenderer
 */
class TwigNamespaceModuleRenderer extends AbstractTwigModuleRenderer
{
    private string $twigNamespace;
    private string $fileExtension;
    private readonly string $pathSeparator;
    private readonly int $pathMaxDepth;

    /**
     * SimpleTwigModule constructor.
     */
    public function __construct(
        Environment $environment,
        ContentRendererConfig $config,
    ) {
        parent::__construct($environment);
        $baseTwigNamespace = str_replace('(at)', '@', $config->getTwigNamespace());
        $this->twigNamespace = trim($baseTwigNamespace, '/');
        $this->fileExtension = $config->getTwigExtension();
        $this->pathSeparator = $config->getTwigPathSeparator();
        $this->pathMaxDepth = $config->getTwigPathMaxDepth();
        $this->debugMode = $config->getTwigDebugMode();
    }

    public function getTemplateForModuleType(string $nodeType): string
    {
        return $this->addExtension($this->twigNamespace.'/'.$this->convertToPath($nodeType));
    }

    protected function addExtension(string $twigPath): string
    {
        $extension = $this->getFileExtension();
        if (str_ends_with($twigPath, $extension)) {
            return $twigPath;
        }

        return $twigPath.$this->getFileExtension();
    }

    public function getTwigNamespace(): string
    {
        return $this->twigNamespace;
    }

    public function setTwigNamespace(string $twigNamespace): self
    {
        $this->twigNamespace = $twigNamespace;

        return $this;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    private function convertToPath(string $originalFileName): string
    {
        if ('' === $this->pathSeparator) {
            return $originalFileName;
        }

        $levels = explode($this->pathSeparator, $originalFileName);
        if (1 === count($levels)) {
            return $originalFileName;
        }

        $representativeLevels = array_splice($levels, 0, min($this->pathMaxDepth, count($levels) - 1));
        $representativeLevels[] = implode($this->pathSeparator, $levels);

        return implode(DIRECTORY_SEPARATOR, $representativeLevels);
    }
}
