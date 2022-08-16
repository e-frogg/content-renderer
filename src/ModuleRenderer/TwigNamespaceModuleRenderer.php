<?php

declare(strict_types=1);

namespace Efrogg\ContentRenderer\ModuleRenderer;


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
 * @package Efrogg\ContentRenderer\ModuleRenderer
 */
class TwigNamespaceModuleRenderer extends AbstractTwigModuleRenderer
{
    protected string $twigNamespace;
    protected string $fileExtension;

    protected string $pathSeparator = '-';
    protected int $maxPathDepth = 2;

    /**
     * SimpleTwigModule constructor.
     *
     * @param Environment $environment
     * @param string      $baseTwigNamespace
     * @param string      $fileExtension
     */
    public function __construct(Environment $environment, string $baseTwigNamespace = '', string $fileExtension = '.twig')
    {
        parent::__construct($environment);
        $this->twigNamespace = trim($baseTwigNamespace, '/');
        $this->fileExtension = $fileExtension;
    }

    public function getTemplateForModuleType(string $nodeType):string
    {
        return $this->addExtension($this->twigNamespace . '/' . $this->computeNodeFileName($nodeType));
    }

    protected function addExtension(string $twigPath): string
    {
        $extension = $this->getFileExtension();
        if (str_ends_with($twigPath, $extension)) {
            return $twigPath;
        }

        return $twigPath.$this->getFileExtension();
    }

    /**
     * @return mixed
     */
    public function getTwigNamespace()
    {
        return $this->twigNamespace;
    }

    public function setTwigNamespace(string $twigNamespace): self
    {
        $this->twigNamespace = $twigNamespace;
        return $this;
    }


    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @param string $fileExtension
     *
     * @return self
     */
    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }


    /**
     * @param string $nodeType
     *
     * @return string
     */
    private function computeNodeFileName(string $nodeType): string
    {
        if(empty($this->pathSeparator)) {
            return $nodeType;
        }

        $parts = explode($this->pathSeparator, $nodeType);
        $lastPart = array_pop($parts);
        $folderParts = array_splice($parts, 0, $this->maxPathDepth);
        $parts[] = $lastPart;

        dump($parts, $folderParts, $nodeType);
        $folderParts[] = implode($this->pathSeparator, $parts);
        return implode(DIRECTORY_SEPARATOR, $folderParts);
    }
}
