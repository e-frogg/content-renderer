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
     * @var null
     */
    protected $pathSeparator;
    protected $pathMaxDepth;

    /**
     * SimpleTwigModule constructor.
     *
     * @param Environment $environment
     * @param string      $baseTwigNamespace
     * @param string      $fileExtension
     * @param string      $pathSeparator
     * @param int         $pathMaxDepth
     */
    public function __construct(Environment $environment,$baseTwigNamespace='',$fileExtension='.twig', $pathSeparator='-',$pathMaxDepth=0)
    {
        parent::__construct($environment);
        $baseTwigNamespace=str_replace('(at)','@',$baseTwigNamespace);
        $this->twigNamespace = trim($baseTwigNamespace,'/');
        $this->fileExtension = $fileExtension;
        $this->pathSeparator = $pathSeparator;
        $this->pathMaxDepth = $pathMaxDepth;
    }

    public function getTemplateForModuleType(string $nodeType):string
    {
        return $this->addExtension($this->twigNamespace.'/'.$this->convertToPath($nodeType));
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

    private function convertToPath(string $originalFileName): string
    {
        $levels = explode($this->pathSeparator,$originalFileName);
        if(count($levels) === 1) {
            return $originalFileName;
        }

//        $fileName = array_pop($levels);
        $representativeLevels = array_splice($levels,0,min($this->pathMaxDepth,count($levels)-1));
        $representativeLevels[]=implode($this->pathSeparator,$levels);
        return implode(DIRECTORY_SEPARATOR,$representativeLevels);
    }
}
