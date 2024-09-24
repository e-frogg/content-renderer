<?php


namespace Efrogg\ContentRenderer;



use Efrogg\ContentRenderer\Converter\Keyword;
use Efrogg\ContentRenderer\Core\MagicObject;

/**
 * Node is solvable for ModuleResolver
 *
 * Class Node
 * @property ?string _type
 * @package Efrogg\ContentRenderer
 */
class Node extends MagicObject
{
    /**
     * @var array<string,mixed>
     */
    private array $context;
    /**
     * @var int|string|null
     */
    private mixed $nodeId;

    /**
     * @param array<string,mixed> $data
     * @param array<string,mixed> $context
     * @param array<string,mixed> ...$extraDatas
     */
    public function __construct(array $data = [], array $context = [], ...$extraDatas)
    {
        parent::__construct($data, ...$extraDatas);
        $this->context = $context;
        $this->nodeId = $this->guessId();
    }

    public function getType(): ?string
    {
        return $this->__get('_type');
    }

    /**
     * @return array<string,mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array<string,mixed> $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * essaye de trouver l'id
     *
     * @return int|string|null
     */
    private function guessId(): int|string|null
    {
        if ($this->__isset(Keyword::NODE_UID)) {
            return $this->__get(Keyword::NODE_UID);
        }
        if ($this->__isset(Keyword::NODE_ID)) {
            return $this->__get(Keyword::NODE_ID);
        }
        if (isset($this->context[Keyword::NODE_ID])) {
            return $this->context[Keyword::NODE_ID];
        }
        if (isset($this->context['id'])) {
            return $this->context['id'];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getNodeId(): mixed
    {
        return $this->nodeId;
    }

    public function isPreview(): bool
    {
        if ($this->__isset(Keyword::PREVIEW)) {
            return (bool)$this->__get(Keyword::PREVIEW);
        }
        return false;
    }

}
