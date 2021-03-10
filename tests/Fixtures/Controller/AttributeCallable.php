<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests\Fixtures\Controller;

use Premier\ArgumentResolver\Tests\Fixtures\Attribute\Foo;

class AttributeCallable
{
    public function action(#[Foo('bar')] string $baz)
    {
    }

    public function invalidAction(#[Foo('bar'), Foo('bar')] string $baz)
    {
    }
}
