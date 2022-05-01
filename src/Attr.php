<?php

declare(strict_types=1);

namespace Yediyuz\Helpers;

class Attr extends AbstractAttr
{
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}
