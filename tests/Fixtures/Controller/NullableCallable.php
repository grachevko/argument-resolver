<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests\Fixtures\Controller;

class NullableCallable
{
    public function action(?string $foo, ?\stdClass $bar, ?string $baz = 'value', string $last = '')
    {
    }
}
