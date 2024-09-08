<?php

declare(strict_types=1);

namespace Yediyuz\Helpers;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use JsonSerializable;
use ReturnTypeWillChange;

abstract class AbstractAttr implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    protected array $attributes;

    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset(string $key)
    {
        unset($this->attributes[$key]);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function offsetExists($key): bool
    {
        return ! is_null($this->getAttribute($key));
    }

    #[ReturnTypeWillChange]
    public function offsetGet($key): mixed
    {
        return $this->getAttribute($key);
    }

    #[ReturnTypeWillChange]
    public function offsetSet($key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    #[ReturnTypeWillChange]
    public function offsetUnset($key): void
    {
        unset($this->attributes[$key]);
    }

    public function toArray(): array
    {
        $keys = array_keys($this->attributes);

        return array_map(function (string $key) {
            return $this->getAttribute($key);
        }, array_combine($keys, $keys));
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @throws \Yediyuz\Helpers\Exceptions\JsonException
     */
    public function toJson($options = 0): string
    {
        return Json::encodeOrFail($this->toArray(), $options);
    }

    protected static function getAccessorMethodName(string $key): string
    {
        return 'get' . Str::studly($key) . 'Attribute';
    }

    protected static function getMutatorMethodName(string $key): string
    {
        return 'set' . Str::studly($key) . 'Attribute';
    }

    protected function getAttribute(string $key): mixed
    {
        $value = $this->attributes[$key] ?? null;

        $accessorFunctionName = self::getAccessorMethodName($key);

        if (method_exists($this, $accessorFunctionName)) {
            return $this->$accessorFunctionName($value);
        }

        return $value;
    }

    protected function setAttribute(string $key, mixed $value): void
    {
        $mutatorFunctionName = self::getMutatorMethodName($key);

        if (method_exists($this, $mutatorFunctionName)) {
            $this->$mutatorFunctionName($value);
            return;
        }

        $this->attributes[$key] = $value;
    }
}
