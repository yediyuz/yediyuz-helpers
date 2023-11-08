<?php

declare(strict_types=1);

namespace Yediyuz\Helpers;

class ArrayHelper
{
    /*
     * Merge multiple arrays in one.
     *
     * Note: If one of the same items is empty, it overwrites the other.
     */
    public static function extend(array ...$arrays): array
    {
        return self::process(false, ...$arrays);
    }

    /**
     * Unlike extend(), it forces overwrite.
     */
    public static function replace(array ...$arrays): array
    {
        return self::process(true, ...$arrays);
    }

    private static function process(bool $force, array ...$arrays): array
    {
        $newArray = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($newArray[$key] ?? []) && is_array($value)) {
                    $newArray[$key] ??= [];
                    $newArray[$key] = self::process($force, $newArray[$key], $value);
                } elseif ($force || ! isset($newArray[$key])) {
                    $newArray[$key] = $value;
                }
            }
        }

        return $newArray;
    }
}
