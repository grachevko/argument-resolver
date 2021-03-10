<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

final class ArrayVariadicValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(mixed $data, ArgumentMetadata $argument): bool
    {
        return $argument->isVariadic() && \is_array($data) && \array_key_exists($argument->getName(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(mixed $data, ArgumentMetadata $argument): iterable
    {
        $values = $data[$argument->getName()];

        if (!\is_array($values)) {
            throw new \InvalidArgumentException(sprintf('The action argument "...$%1$s" is required to be an array, the request attribute "%1$s" contains a type of "%2$s" instead.', $argument->getName(), get_debug_type($values)));
        }

        yield from $values;
    }
}
