<?php


namespace Efrogg\ContentRenderer;


use Efrogg\ContentRenderer\Converter\ArrayConverter;
use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Core\ConfiguratorInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Module\ModuleResolver;
use Efrogg\ContentRenderer\ModuleRenderer\ModuleRendererResolver;
use Efrogg\ContentRenderer\NodeProvider\NodeProviderInterface;
use LogicException;
use Twig\Template;

class CmsRenderer implements DecoratorAwareInterface, ParameterizableInterface
{
    use DecoratorAwareTrait;
    use ParameterizableTrait;

    /**
     * @var ModuleResolver
     */
    private $moduleResolver;

    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;

    /**
     * @var ModuleRendererResolver
     */
    private $moduleRendererResolver;
    /**
     * @var ArrayConverter
     */
    private $converter;

    private $debugMode = false;

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @param bool $debugMode
     *
     * @return CmsRenderer
     */
    public function setDebugMode(bool $debugMode): CmsRenderer
    {
        $this->debugMode = $debugMode;
        return $this;
    }

    /**
     * Renderer constructor.
     *
     * @param ModuleResolver         $moduleResolver
     * @param ModuleRendererResolver $moduleRendererResolver
     */
    public function __construct(ModuleResolver $moduleResolver, ModuleRendererResolver $moduleRendererResolver)
    {
        $this->moduleResolver = $moduleResolver;
        $this->moduleRendererResolver = $moduleRendererResolver;
        $this->converter = new ArrayConverter();
    }

    /**
     * @param $data
     *
     * @return string
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws Exception\InvalidDataException
     * @throws LogicException
     */
    public function convertAndRender($data): string
    {
        if ($data instanceof Node) {
            return $this->render($data);
        }
        if (is_array($data)) {
            return $this->render($this->converter->convert($data));
        }
        throw new LogicException('data must be Node or valid array');
    }

    /**
     * @param Node $node
     *
     * @return string
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws LogicException
     */
    public function render(Node $node): string
    {
        if (null === $this->moduleResolver) {
            throw new LogicException('moduleResolver is not present');
        }
        if (null === $this->moduleRendererResolver) {
            throw new LogicException('moduleRendererResolver is not present');
        }
        $module = $this->moduleResolver->resolve($node);
        $renderer = $this->moduleRendererResolver->resolve($module);

        $renderer->setParameters($this->getParameters());
        return $this->decorate($renderer->render($module, $node));
    }
    //TODO : dataProvider sur type de node => page => tpl (h1 etc.....)


    /**
     * @param string      $nodeId
     * @param string|null $subNode
     * @param bool        $useSafeFallback
     *  permet de retomber sur un node "error" proprement
     *
     * @return string
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws LogicException
     */
    public function renderNodeById(string $nodeId, string $subNode = null, bool $useSafeFallback = true): string
    {
        if (null === $this->nodeProvider) {
            throw new LogicException('there is no nodeProvider configured');
        }
        try {
            $node = $this->nodeProvider->getNodeById($nodeId);
        } catch (NodeNotFoundException $exception) {
            $stack = [];
            foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT) as $stackItem) {
                $object = $stackItem['object'];
                if ($stackItem['class'] === Template::class && 'display' === $stackItem['function']) {
                    $stack[] = $object->getTemplateName();
                }
            }
            $node = new Node(
                [
                    Keyword::NODE_TYPE => 'nodeNotFound',
                    "nodeId"           => $nodeId,
                    "subNode"          => $subNode,
                    "stack"            => var_export($stack, true),
                    "debug"            => $this->isDebugMode()
                ]
            );
            $subNode = null;
        }
        if (null !== $subNode) {
            foreach (explode('.', $subNode) as $subnodeKey) {
                $node = $node->getData()[$subnodeKey];
            }
        }

        if (is_array($node)) {
            return implode(
                '',
                array_map(
                    function ($node) {
                        return $this->render($node);
                    },
                    $node
                )
            );
        }
        return $this->render($node);
    }


    /**
     * @param  NodeProviderInterface  $dataProvider
     * @return CmsRenderer
     */
    public function setNodeProvider(NodeProviderInterface $dataProvider): CmsRenderer
    {
        $this->nodeProvider = $dataProvider;
        return $this;
    }

    /**
     * @return NodeProviderInterface
     */
    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }

    public function initConfigurator(ConfiguratorInterface $configurator): void
    {
        $configurator->configure();
    }

}
