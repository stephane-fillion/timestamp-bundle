<?php

declare(strict_types=1);

namespace TimestampBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TimestampBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
