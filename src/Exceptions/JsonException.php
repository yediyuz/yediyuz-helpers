<?php

declare(strict_types=1);

namespace Yediyuz\Helpers\Exceptions;

use JsonException as ParentJsonException;

class JsonException extends ParentJsonException
{
    public static function jsonError(): self
    {
        return new self(\json_last_error_msg(), \json_last_error());
    }
}
