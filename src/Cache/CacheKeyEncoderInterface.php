<?php

namespace Efrogg\ContentRenderer\Cache;

interface CacheKeyEncoderInterface
{
    /**
     * @param string $nodeIdWithPrefix
     *
     * @return string
     */
    public function encodeKey(string $nodeIdWithPrefix): string;
}
