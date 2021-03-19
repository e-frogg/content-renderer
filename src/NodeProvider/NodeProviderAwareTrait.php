<?php


namespace Efrogg\ContentRenderer\NodeProvider;


trait NodeProviderAwareTrait
{
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;

    /**
     * @return NodeProviderInterface
     */
    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }

    /**
     * @param NodeProviderInterface $nodeProvider
     */
    public function setNodeProvider(NodeProviderInterface $nodeProvider): void
    {
        $this->nodeProvider = $nodeProvider;
    }


}