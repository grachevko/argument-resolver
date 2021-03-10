<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

final class DefaultValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(mixed $data, ArgumentMetadata $argument): bool
    {
        return $argument->hasDefaultValue() || (null !== $argument->getType() && $argument->isNullable() && !$argument->isVariadic());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(mixed $data, ArgumentMetadata $argument): iterable
    {
        yield $argument->hasDefaultValue() ? $argument->getDefaultValue() : null;
    }
}
