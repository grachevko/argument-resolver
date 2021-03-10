<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

interface ArgumentMetadataFactoryInterface
{
    /**
     * @return ArgumentMetadata[]
     */
    public function createArgumentMetadata(mixed $callable): array;
}
