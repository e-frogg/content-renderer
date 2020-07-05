<?php


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
    /** @var string */
    protected $twigNamespace;
    /** @var string */
    protected $fileExtension;

    /**
     * SimpleTwigModule constructor.
     * @param  Environment  $environment
     * @param  string         $baseTwigNamespace
     * @param  string       $fileExtension
     */
    public function __construct(Environment $environment,$baseTwigNamespace='',$fileExtension='.twig')
    {
        parent::__construct($environment);
        $this->twigNamespace = $baseTwigNamespace;
        $this->fileExtension = $fileExtension;
    }

    public function getTemplateForModuleType(string $nodeType):string
    {
        return $this->addExtension($this->twigNamespace.'/'.$nodeType);
    }

    protected function addExtension(string $twigPath): string
    {
        $extension = $this->getFileExtension();
        if($extension === substr($twigPath, -strlen($extension))) {
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

    /**
     * @param  mixed  $twigNamespace
     * @return self
     */
    public function setTwigNamespace($twigNamespace): self
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
     * @param  string  $fileExtension
     * @return self
     */
    public function setFileExtension(string $fileExtension): self
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }
}