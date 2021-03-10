<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

interface ArgumentValueResolverInterface
{
    /**
     * Whether this resolver can resolve the value for the given ArgumentMetadata.
     */
    public function supports(mixed $data, ArgumentMetadata $argument): bool;

    /**
     * Returns the possible value(s).
     */
    public function resolve(mixed $data, ArgumentMetadata $argument): iterable;
}
