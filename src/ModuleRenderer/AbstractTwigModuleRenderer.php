<?php


namespace Efrogg\ContentRenderer\ModuleRenderer;


use Efrogg\ContentRenderer\Module\DataModuleInterface;
use Efrogg\ContentRenderer\Module\ModuleInterface;
use Efrogg\ContentRenderer\Node;
use Efrogg\ContentRenderer\ParameterizableTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * this renderer handles rendering modules through twig templates.
 * the modules needs to implement TwigModuleInterface
 * data is provided by the module
 * this class cannot be instanced as is, because twig template should be resolved in an extending implementation
 *
 * Class AbstractTwigModuleRenderer
 * @package Efrogg\ContentRenderer\ModuleRenderer
 */
abstract class AbstractTwigModuleRenderer implements ModuleRendererInterface, LoggerAwareInterface
{
    use ParameterizableTrait;
    use LoggerAwareTrait;

    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $debugMode = false;

    /**
     * TwigModuleRenderer constructor.
     * @param  Environment  $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }


    public function canResolve($solvable, string $resolverName): bool
    {
        return $solvable instanceof DataModuleInterface;
    }

    public function getPriority(): int
    {
        return 1;
    }

    abstract public function getTemplateForModuleType(string $nodeType): string;

    /**
     * @param  DataModuleInterface  $module
     * @param  Node                 $node
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(ModuleInterface $module, Node $node): string
    {
        $twigData = $module->getNodeData($node);
        if (null !== $this->getParameters()) {
            $twigData = array_merge($twigData, $this->getParameters()->all());
        }

        $templateName = $this->getTemplateForModuleType($node->getType());

        try {
            return $this->environment->render(
                $templateName,
                $twigData
            );
        } catch (LoaderError $e) {
            if (null !== $this->logger) {
                $this->logger->error(sprintf("missing template %s", $templateName));
            }
            if ($this->debugMode) {
                $missingTpl = $this->getTemplateForModuleType('missingTemplate');
                try {
                    return $this->environment->render(
                        $missingTpl,
                        ['templateName' => $templateName]
                    );
                } catch (LoaderError ) {
                    if (null !== $this->logger) {
                        $this->logger->error(sprintf("missing template %s", $missingTpl));
                    }
                    return sprintf("-- missing template %s --", $templateName);
                }
            }
            return '';
//            throw $e;
        }
    }

    /**
     * @param bool $debugMode
     */
    public function setDebugMode(bool $debugMode): void
    {
        $this->debugMode = $debugMode;
    }
}
