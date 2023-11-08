<?php

declare(strict_types=1);

namespace Yediyuz\Helpers;

use Exception;
use Illuminate\Support\Arr;
use JsonSerializable;
use TypeError;
use Yediyuz\Helpers\Exceptions\JsonException;

class Json
{
    /**
     * @throws \Yediyuz\Helpers\Exceptions\JsonException
     */
    public static function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0): mixed
    {
        $data = \json_decode($json, $assoc, $depth, $options); // @phpstan-ignore-line
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonException::jsonError();
        }

        return $data;
    }

    /**
     * @throws \Yediyuz\Helpers\Exceptions\JsonException
     */
    public static function encode(mixed $value, int $options = 0): string
    {
        error_clear_last();
        $result = false;

        try {
            $result = json_encode(self::jsonSerialize($value), $options|JSON_THROW_ON_ERROR);
        } catch (\JsonException|TypeError $e) {
            throw new JsonException($e->getMessage(), $e->getCode());
        }

        if ($result === false) { // @phpstan-ignore-line
            throw JsonException::jsonError();
        }

        return $result;
    }

    public static function decodeOr(?string $json, bool $assoc = false, mixed $callbackOrValue = null, int $depth = 512, int $options = 0): mixed
    {
        $result = false;
        $error = false;

        try {
            $result = \json_decode($json, $assoc, $depth, $options); // @phpstan-ignore-line
        } catch (\JsonException|TypeError $e) {
            $error = true;
        }

        if (! $error && $result === false) {
            $error = true;
        }

        if ($error || JSON_ERROR_NONE !== json_last_error()) {
            if (is_callable($callbackOrValue)) {
                return $callbackOrValue();
            }

            return $callbackOrValue;
        }

        return $result;
    }

    public static function encodeOr(mixed $value, mixed $callbackOrValue, int $options = 0): mixed
    {
        error_clear_last();

        $result = false;
        $error = false;

        try {
            $result = json_encode(self::jsonSerialize($value), $options|JSON_THROW_ON_ERROR);
        } catch (\JsonException|TypeError $e) {
            $error = true;
        }

        if (! $error && $result === false) {
            $error = true;
        }

        if ($error) {
            if (is_callable($callbackOrValue)) {
                return $callbackOrValue();
            }

            return $callbackOrValue;
        }

        return $result;
    }

    /**
     * @template T of \Yediyuz\Helpers\Exceptions\JsonException
     *
     * @param array<class-string<T>, array|string|null>|array<class-string<T>>|T|string $exception
     *
     * @throws \Yediyuz\Helpers\Exceptions\JsonException|T
     */
    public static function decodeOrFail(mixed $json, bool $assoc = false, array|Exception|string $exception = JsonException::class, int $depth = 512, int $options = 0): mixed
    {
        $data = \json_decode($json, $assoc, $depth, $options); // @phpstan-ignore-line

        if (JSON_ERROR_NONE !== json_last_error()) {
            $errorParams = [\json_last_error_msg(), \json_last_error()];
            if (is_array($exception)) {
                $parameters = Arr::wrap($exception[1] ?? $errorParams);
                $exception = $exception[0];
            } else {
                $parameters = $errorParams;
            }

            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            /** @var string|object $exception */

            throw is_string($exception) ? new JsonException($exception) : $exception;
        }

        return $data;
    }

    /**
     * @template T of \Yediyuz\Helpers\Exceptions\JsonException|\Exception
     *
     * @param array<class-string<T>, array|string|null>|array<class-string<T>>|T|string $exception
     *
     * @throws \Yediyuz\Helpers\Exceptions\JsonException|T
     */
    public static function encodeOrFail(mixed $value, int $options = 0, Exception|array|string $exception = JsonException::class): string
    {
        error_clear_last();

        $errorParams = null;
        $result = false;

        try {
            $result = json_encode(self::jsonSerialize($value), $options|JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $errorParams = [$e->getMessage(), $e->getCode()];
        }

        if (is_null($errorParams) && $result === false) {
            $errorParams = [];
        }

        if (is_array($errorParams)) {
            if (is_array($exception)) {
                $parameters = Arr::wrap($exception[1] ?? $errorParams);
                $exception = $exception[0];
            } else {
                $parameters = $errorParams;
            }

            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            /** @var string|object $exception */

            throw is_string($exception) ? new JsonException($exception) : $exception;
        }

        return $result;
    }

    private static function jsonSerialize(mixed $data): mixed
    {
        if (
            ! is_array($data)
            && ! $data instanceof JsonSerializable
            && (! class_exists(\Illuminate\Contracts\Support\Jsonable::class) || ! $data instanceof \Illuminate\Contracts\Support\Jsonable)
            && (! class_exists(\Illuminate\Contracts\Support\Arrayable::class) || ! $data instanceof \Illuminate\Contracts\Support\Arrayable)
        ) {
            return $data;
        }

        return collect($data)->map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            }

            if (class_exists(\Illuminate\Contracts\Support\Jsonable::class) && $value instanceof \Illuminate\Contracts\Support\Jsonable) {
                return json_decode($value->toJson(), true);
            }

            if (class_exists(\Illuminate\Contracts\Support\Arrayable::class) && $value instanceof \Illuminate\Contracts\Support\Arrayable) {
                return $value->toArray();
            }

            return $value;
        })->toArray();
    }
}
