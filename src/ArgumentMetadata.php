<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver;

class ArgumentMetadata
{
    private bool $isNullable;

    public function __construct(
        private string $name,
        private ?string $type,
        private bool $isVariadic,
        private bool $hasDefaultValue,
        private mixed $defaultValue,
        bool $isNullable = false,
        private ?ArgumentInterface $attribute = null,
    ) {
        $this->isNullable = $isNullable || null === $type || ($hasDefaultValue && null === $defaultValue);
    }

    /**
     * Returns the name as given in PHP, $foo would yield "foo".
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the type of the argument.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Returns whether the argument is defined as "...$variadic".
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }

    /**
     * Returns whether the argument has a default value.
     *
     * Implies whether an argument is optional.
     */
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * Returns whether the argument accepts null values.
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * Returns the default value of the argument.
     *
     * @throws \LogicException if no default value is present; {@see self::hasDefaultValue()}
     */
    public function getDefaultValue(): mixed
    {
        if (!$this->hasDefaultValue) {
            throw new \LogicException(sprintf('Argument $%s does not have a default value. Use "%s::hasDefaultValue()" to avoid this exception.', $this->name, __CLASS__));
        }

        return $this->defaultValue;
    }

    /**
     * Returns the attribute (if any) that was set on the argument.
     */
    public function getAttribute(): ?ArgumentInterface
    {
        return $this->attribute;
    }
}
