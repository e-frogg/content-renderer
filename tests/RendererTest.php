<?php


use Efrogg\ContentRenderer\Asset\AssetResolver;
use Efrogg\ContentRenderer\CmsRenderer;
use Efrogg\ContentRenderer\DataProvider\DataProviderResolver;
use Efrogg\ContentRenderer\Module\ModuleResolver;
use Efrogg\ContentRenderer\Module\SimpleDataModule;
use Efrogg\ContentRenderer\ModuleRenderer\ModuleRendererResolver;
use Efrogg\ContentRenderer\ModuleRenderer\TwigNamespaceModuleRenderer;
use Efrogg\ContentRenderer\Node;
use Efrogg\ContentRenderer\NodeProvider\SimpleJsonFileNodeProvider;
use Efrogg\ContentRenderer\twigConfigurator;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RendererTest extends TestCase
{
    /**
     * @var DataProviderResolver
     */
    private $dataProvider;
    /**
     * @var ModuleResolver
     */
    private $moduleResolver;
    /**
     * @var CmsRenderer
     */
    private $renderer;
    /**
     * @var ModuleRendererResolver
     */
    private $moduleRendererResolver;
    /**
     * @var SimpleJsonFileNodeProvider
     */
    private $nodeProvider;
    /**
     * @var FilesystemLoader
     */
    private $twigLoader;
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var AssetResolver
     */
    private $assetResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->twigLoader = new FilesystemLoader([__DIR__.'/../demo/templates']);
        $this->twigEnvironment = new Environment($this->twigLoader);


        $this->dataProvider = new DataProviderResolver();

        $this->moduleResolver = new ModuleResolver($this->dataProvider);
        $this->moduleResolver->addSolver(new SimpleDataModule());

        $this->moduleRendererResolver = new ModuleRendererResolver();
        $this->moduleRendererResolver->addSolver(new TwigNamespaceModuleRenderer($this->twigEnvironment));

        $this->assetResolver = new AssetResolver();

        $this->renderer = new CmsRenderer($this->moduleResolver,$this->moduleRendererResolver);

        $configurator = new twigConfigurator($this->renderer,$this->assetResolver,$this->twigEnvironment);
        $this->renderer->initConfigurator($configurator);

        $this->nodeProvider = new SimpleJsonFileNodeProvider(__DIR__.'/../demo/data');



    }

    public function testJsonConversion()
    {
        $node = $this->nodeProvider->getNodeById('pages/page1');
        $this->assertInstanceOf(Node::class, $node);
        $this->assertEquals('page',$node->getType());
    }

    public function testRender()
    {
        $this->renderer->setNodeProvider($this->nodeProvider);

        $content = $this->renderer->renderNodeById('pages/page1');
        $this->assertStringContainsString('mon title',$content);
        $this->assertStringContainsString('page 1 Demo json',$content);

        $this->assertStringContainsString('paragraph heading 2',$content);
        $this->assertStringContainsString('second paragraph content',$content);
    }
}