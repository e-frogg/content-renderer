<?php

namespace Efrogg\ContentRenderer;

use Efrogg\ContentRenderer\Converter\ArrayConverter;
use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Core\ConfiguratorInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareInterface;
use Efrogg\ContentRenderer\Decorator\DecoratorAwareTrait;
use Efrogg\ContentRenderer\Event\BeforeRenderEvent;
use Efrogg\ContentRenderer\Exception\NodeNotFoundException;
use Efrogg\ContentRenderer\Module\ModuleResolver;
use Efrogg\ContentRenderer\ModuleRenderer\ModuleRendererResolver;
use Efrogg\ContentRenderer\NodeProvider\NodeProviderInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Template;

class CmsRenderer implements DecoratorAwareInterface, ParameterizableInterface, CmsRendererInterface, LoggerAwareInterface
{
    use DecoratorAwareTrait;
    use ParameterizableTrait;
    use LoggerAwareTrait;

    private NodeProviderInterface $nodeProvider;

    private readonly ArrayConverter $converter;

    private bool $debugMode = false;

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function setDebugMode(bool $debugMode): CmsRenderer
    {
        $this->debugMode = $debugMode;

        return $this;
    }

    /**
     * Renderer constructor.
     */
    public function __construct(
        private readonly ModuleResolver $moduleResolver,
        private readonly ModuleRendererResolver $moduleRendererResolver,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        $this->converter = new ArrayConverter();
    }

    /**
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws Exception\InvalidDataException
     * @throws \LogicException
     */
    public function convertAndRender($data, ?array $additionalData = []): ?string
    {
        if ($data instanceof Node) {
            if (!empty($additionalData)) {
                $data->merge($additionalData);
            }

            return $this->render($data);
        }
        if (is_array($data)) {
            return $this->render($this->converter->convert(array_merge($data, $additionalData??[])));
        }

        // if strict mode, throw exception
        if ($this->isDebugMode()) {
            throw new \LogicException('data must be Node or valid array');
        }

        return null;
    }

    public function convertAndRenderMultiple($data, ?array $additionalData = []): ?string
    {
        if (is_string($data)) {
            return $data;
        }
        if (!is_array($data)) {
            if ($this->isDebugMode()) {
                throw new \LogicException('data must be array or string');
            }

            return null;
        }

        return implode('', array_map(fn ($node) => $this->convertAndRender($node, $additionalData), $data));
    }

    /**
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws \LogicException
     */
    public function render(Node $node): string
    {
        if (!isset($this->moduleResolver)) {
            throw new \LogicException('moduleResolver is not present');
        }
        if (!isset($this->moduleRendererResolver)) {
            throw new \LogicException('moduleRendererResolver is not present');
        }

        $this->logger->debug('rendering node '.$node->getType());
        $module = $this->moduleResolver->resolve($node);
        $renderer = $this->moduleRendererResolver->resolve($module);

        $beforeRenderEvent = new BeforeRenderEvent($module, $node);
        $this->eventDispatcher->dispatch($beforeRenderEvent);

        if ($beforeRenderEvent->isHidden()) {
            return '';
        }
        $renderer->setParameters($this->getParameters());

        return $this->decorate($renderer->render($module, $node));
    }

    /**
     * @param string|null $subNode
     *                             permet de retomber sur un node "error" proprement
     *
     * @throws Core\Resolver\Exception\InvalidSolvableException
     * @throws Core\Resolver\Exception\SolverNotFoundException
     * @throws \LogicException
     */
    public function renderNodeById(string $nodeId, ?string $subNode = null): string
    {
        if (!isset($this->nodeProvider)) {
            throw new \LogicException('there is no nodeProvider configured');
        }
        try {
            $node = $this->nodeProvider->getNodeById($nodeId);
        } catch (NodeNotFoundException $exception) {
            $stack = [];
            foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT) as $stackItem) {
                if (isset($stackItem['class']) && Template::class === $stackItem['class'] && 'display' === $stackItem['function']) {
                    $object = $stackItem['object'];
                    $stack[] = $object->getTemplateName();
                }
            }
            $node = new Node(
                [
                    Keyword::NODE_TYPE => 'nodeNotFound',
                    'nodeId' => $nodeId,
                    'subNode' => $subNode,
                    'stack' => var_export($stack, true),
                    'debug' => $this->isDebugMode(),
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

    public function setNodeProvider(NodeProviderInterface $dataProvider): CmsRenderer
    {
        $this->nodeProvider = $dataProvider;

        return $this;
    }

    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }

    public function initConfigurator(ConfiguratorInterface $configurator): void
    {
        $configurator->configure();
    }

    public function setUseCache(bool $useCache = true): void
    {
        $this->nodeProvider->setUseCache($useCache);
    }

    public function setUpdateCache(bool $updateCache = true, bool $isTemporaryChange = false): void
    {
        $this->nodeProvider->setUpdateCache($updateCache, $isTemporaryChange);
    }

    public function isUpdateCache(): bool
    {
        return $this->nodeProvider->isUpdateCache();
    }

    public function isUseCache(): bool
    {
        return $this->nodeProvider->isUseCache();
    }

    public function restoreUpdateCache(): void
    {
        $this->nodeProvider->restoreUpdateCache();
    }

    /**
     * @param array<mixed> $data
     */
    private function isAssociativeArray(array $data): bool
    {
        return !array_is_list($data);
    }
}
