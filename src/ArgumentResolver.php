<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

final class ArgumentResolver implements ArgumentResolverInterface
{
    private ArgumentMetadataFactoryInterface $argumentMetadataFactory;

    /**
     * @var iterable<ArgumentValueResolverInterface>
     */
    private iterable $argumentValueResolvers;

    /**
     * @param iterable<ArgumentValueResolverInterface> $argumentValueResolvers
     */
    public function __construct(
        ArgumentMetadataFactoryInterface $argumentMetadataFactory = null,
        iterable $argumentValueResolvers = []
    ) {
        $this->argumentMetadataFactory = $argumentMetadataFactory ?? new ArgumentMetadataFactory();
        $this->argumentValueResolvers = [] !== $argumentValueResolvers
            ? $argumentValueResolvers
            : [
                new ArrayValueResolver(),
                new ArrayVariadicValueResolver(),
                new DefaultValueResolver(),
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(mixed $data, mixed $callable): array
    {
        $arguments = [];

        foreach ($this->argumentMetadataFactory->createArgumentMetadata($callable) as $metadata) {
            foreach ($this->argumentValueResolvers as $resolver) {
                if (!$resolver->supports($data, $metadata)) {
                    continue;
                }

                $resolved = $resolver->resolve($data, $metadata);

                $atLeastOne = false;
                foreach ($resolved as $append) {
                    $atLeastOne = true;
                    $arguments[] = $append;
                }

                if (!$atLeastOne) {
                    throw new \InvalidArgumentException(sprintf('"%s::resolve()" must yield at least one value.', \get_class($resolver)));
                }

                // continue to the next controller argument
                continue 2;
            }

            $representative = $callable;

            if (\is_array($representative)) {
                $representative = sprintf('%s::%s()', \get_class($representative[0]), $representative[1]);
            } elseif (\is_object($representative)) {
                $representative = \get_class($representative);
            }

            throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument. Either the argument is nullable and no null value has been provided, no default value has been provided or because there is a non optional argument after this one.', $representative, $metadata->getName()));
        }

        return $arguments;
    }
}
