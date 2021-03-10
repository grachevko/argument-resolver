<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests\Fixtures\Controller;

class BasicTypesCallable
{
    public function action(string $foo, int $bar, float $baz)
    {
    }
}
