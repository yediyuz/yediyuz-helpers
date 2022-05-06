<?php

declare(strict_types=1);

namespace Yediyuz\Helpers;

use Illuminate\Support\Arr;

class ClassImplements
{
    /**
     * @param object|class-string $objectOrClass
     * @param class-string        $implement
     */
    public static function has(object|string $objectOrClass, string $implement): bool
    {
        $classImplements = class_implements($objectOrClass);

        if ($classImplements === false) {
            return false;
        }

        return in_array($implement, $classImplements, true);
    }

    /**
     * @param object|class-string         $objectOrClass
     * @param class-string[]|class-string $implements
     */
    public static function hasAny(object|string $objectOrClass, mixed ...$implements): bool
    {
        $classImplements = class_implements($objectOrClass);

        if ($classImplements === false) {
            return false;
        }

        $implements = Arr::flatten($implements);
        $count = count($implements);

        return count(Arr::except(array_flip($implements), $classImplements)) !== $count;
    }

    /**
     * @param object|class-string         $objectOrClass
     * @param class-string[]|class-string $implements
     */
    public static function hasAll(object|string $objectOrClass, mixed ...$implements): bool
    {
        $classImplements = class_implements($objectOrClass);

        if ($classImplements === false) {
            return false;
        }

        $implements = Arr::flatten($implements);

        return blank(Arr::except(array_flip($implements), $classImplements));
    }
}
