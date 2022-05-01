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
        return self::process(false, false, ...$arrays);
    }

    /**
     * Unlike extend(), it forces overwrite.
     */
    public static function replace(array ...$arrays): array
    {
        return self::process(true, false, ...$arrays);
    }

    private static function process(bool $force = false, bool $merge = false, array &$arrays = []): array
    {
        $args = func_get_args();
        array_shift($args);
        $argsCount = count($args);

        for ($i = 1; $i < $argsCount; $i++) {
            // extend current result:
            foreach ($args[$i] as $k => $v) {
                if (! isset($arrays[$k])) {
                    $arrays[$k] = $v;
                } elseif (is_array($arrays[$k]) && is_array($v)) {
                    self::process($force, $merge, $arrays[$k], $v);
                } elseif ($force || blank($arrays[$k])) {
                    $arrays[$k] = $v;
                }
            }
        }

        return $arrays;
    }
}
