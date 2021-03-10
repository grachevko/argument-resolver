<?php

declare(strict_types=1);

namespace Premier\ArgumentResolver\Tests\Fixtures\Controller;

class VariadicCallable
{
    public function action($foo, ...$bar): void
    {
    }
}
