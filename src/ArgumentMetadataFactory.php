<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

final class ArgumentMetadataFactory implements ArgumentMetadataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createArgumentMetadata(mixed $callable): array
    {
        $arguments = [];

        if (\is_array($callable)) {
            $reflection = new \ReflectionMethod($callable[0], $callable[1]);
        } elseif (\is_object($callable) && !$callable instanceof \Closure) {
            $reflection = (new \ReflectionObject($callable))->getMethod('__invoke');
        } else {
            $reflection = new \ReflectionFunction($callable);
        }

        foreach ($reflection->getParameters() as $param) {
            $attribute = null;
            $reflectionAttributes = $param->getAttributes(ArgumentInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

            if (\count($reflectionAttributes) > 1) {
                $representative = $callable;

                if (\is_array($representative)) {
                    $representative = sprintf('%s::%s()', \get_class($representative[0]), $representative[1]);
                } elseif (\is_object($representative)) {
                    $representative = \get_class($representative);
                }

                throw new InvalidMetadataException(sprintf('Controller "%s" has more than one attribute for "$%s" argument.', $representative, $param->getName()));
            }

            if (isset($reflectionAttributes[0])) {
                $attribute = $reflectionAttributes[0]->newInstance();
            }

            \assert($attribute instanceof ArgumentInterface);

            $arguments[] = new ArgumentMetadata($param->getName(), $this->getType($param, $reflection), $param->isVariadic(), $param->isDefaultValueAvailable(), $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null, $param->allowsNull(), $attribute);
        }

        return $arguments;
    }

    /**
     * Returns an associated type to the given parameter if available.
     */
    private function getType(\ReflectionParameter $parameter, \ReflectionFunctionAbstract $function): ?string
    {
        if (null === ($type = $parameter->getType())) {
            return null;
        }
        $name = $type instanceof \ReflectionNamedType ? $type->getName() : (string) $type;

        if ($function instanceof \ReflectionMethod) {
            $lcName = strtolower($name);

            switch ($lcName) {
                case 'self':
                    return $function->getDeclaringClass()->name;
                case 'parent':
                    return false !== ($parent = $function->getDeclaringClass()->getParentClass()) ? $parent->name : null;
            }
        }

        return $name;
    }
}
