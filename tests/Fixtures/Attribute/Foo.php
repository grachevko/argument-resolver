<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests\Fixtures\Attribute;

use Premier\ArgumentResolver\ArgumentInterface;

#[Attribute(\Attribute::TARGET_PARAMETER)]
class Foo implements ArgumentInterface
{
    private $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }
}
