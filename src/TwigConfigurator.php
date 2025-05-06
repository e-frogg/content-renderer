<?php


namespace Efrogg\ContentRenderer;


use Efrogg\ContentRenderer\Core\ConfiguratorInterface;
use Efrogg\ContentRenderer\Event\TwigConfigurationEvent;
use LogicException;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Runtime\EscaperRuntime;

class TwigConfigurator implements ConfiguratorInterface
{

    public const PRIORITY_HIGH=0;
    public const PRIORITY_NORMAL=10;
    public const PRIORITY_LOW=20;

    protected EventDispatcherInterface $eventDispatcher;
    protected FileLocator $fileLocator;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * TwigRenderer constructor.
     *
     * @param  Environment   $environment
     */
    public function __construct(
        Environment $environment,
        EventDispatcherInterface $eventDispatcher,
        FileLocator $fileLocator
    )
    {
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @throws LogicException
     */
    public function configure(): void
    {
        $this->environment->getRuntime(EscaperRuntime::class)->setEscaper(
            'json_string',
            $this->jsonStringEscape(...)
        );

        // déclenche l'event pour ajouter
//        $loader = $this->environment->getLoader();
//        if ($loader instanceof ChainLoader) {
//            $pathCollector = new TwigPathCollector();
//            $this->eventDispatcher->dispatch(new TwigConfigurationEvent($this->environment, $pathCollector));
//
//            $filesystemLoader = new FilesystemLoader();
//            $filesystemLoader->setPaths($pathCollector->getSortedPaths(), 'CMS');
//            $loader->addLoader($filesystemLoader);
//        }
    }

    public function jsonStringEscape(Environment $_twig, ?string $string): ?string
    {
        if (null === $string) {
            return null;
        }
        return str_replace('"', '\\"', $string);
    }
}
