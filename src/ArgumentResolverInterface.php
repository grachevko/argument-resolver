<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

interface ArgumentResolverInterface
{
    /**
     * @throws \RuntimeException When no value could be provided for a required argument
     */
    public function getArguments(mixed $data, mixed $callable): array;
}
