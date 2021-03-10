<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

final class ArrayValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(mixed $data, ArgumentMetadata $argument): bool
    {
        return !$argument->isVariadic() && \is_array($data) && \array_key_exists($argument->getName(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(mixed $data, ArgumentMetadata $argument): iterable
    {
        yield $data[$argument->getName()];
    }
}
