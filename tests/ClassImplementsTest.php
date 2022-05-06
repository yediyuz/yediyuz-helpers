<?php

declare(strict_types=1);

use Yediyuz\Helpers\ClassImplements;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\AbstractClass;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\AbstractClassInterface;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\AbstractClassInterface2;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\ParentClass;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\ParentClassInterface;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\SubClass;
use Yediyuz\Helpers\Tests\Fixtures\ClassImplements\SubClassInterface;

test('purpose of usage', function () {
    $array = [
        'sub_class' => SubClass::class,
    ];

    $instanceOf = $array['sub_class'] instanceof SubClassInterface;

    $this->assertFalse($instanceOf);

    $this->assertTrue(ClassImplements::has($array['sub_class'], SubClassInterface::class));
    $this->assertTrue(ClassImplements::has(new $array['sub_class'], SubClassInterface::class));
});

test('has', function () {
    $this->assertTrue(ClassImplements::has(new SubClass, SubClassInterface::class));
    $this->assertTrue(ClassImplements::has(SubClass::class, SubClassInterface::class));
    $this->assertTrue(ClassImplements::has(SubClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::has(SubClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::has(SubClass::class, AbstractClassInterface2::class));
    $this->assertTrue(ClassImplements::has(ParentClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::has(ParentClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::has(ParentClass::class, AbstractClassInterface2::class));
    $this->assertTrue(ClassImplements::has(AbstractClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::has(AbstractClass::class, AbstractClassInterface2::class));

    $this->assertFalse(ClassImplements::has(ParentClass::class, SubClassInterface::class));
    $this->assertFalse(ClassImplements::has(AbstractClass::class, SubClassInterface::class));
    $this->assertFalse(ClassImplements::has(AbstractClass::class, ParentClassInterface::class));
});

test('hasAny', function () {
    $this->assertTrue(ClassImplements::hasAny(new SubClass, [SubClassInterface::class]));
    $this->assertTrue(ClassImplements::hasAny(SubClass::class, [SubClassInterface::class]));
    $this->assertTrue(ClassImplements::hasAny(SubClass::class, SubClassInterface::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(SubClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(SubClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(SubClass::class, AbstractClassInterface2::class));
    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, AbstractClassInterface2::class));
    $this->assertTrue(ClassImplements::hasAny(AbstractClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAny(AbstractClass::class, AbstractClassInterface2::class));

    $this->assertFalse(ClassImplements::hasAny(ParentClass::class, SubClassInterface::class));
    $this->assertFalse(ClassImplements::hasAny(AbstractClass::class, SubClassInterface::class));
    $this->assertFalse(ClassImplements::hasAny(AbstractClass::class, ParentClassInterface::class));

    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, [AbstractClassInterface2::class]));
    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
    $this->assertTrue(ClassImplements::hasAny(ParentClass::class, SubClassInterface::class, ParentClassInterface::class));
    $this->assertFalse(ClassImplements::hasAny(AbstractClass::class, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
});

test('hasAll', function () {
    $this->assertTrue(ClassImplements::hasAll(new SubClass, SubClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, SubClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(new SubClass, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, SubClassInterface::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(SubClass::class, AbstractClassInterface2::class));
    $this->assertFalse(ClassImplements::hasAll(ParentClass::class, SubClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, AbstractClassInterface2::class));
    $this->assertFalse(ClassImplements::hasAll(AbstractClass::class, SubClassInterface::class));
    $this->assertFalse(ClassImplements::hasAll(AbstractClass::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(AbstractClass::class, AbstractClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(AbstractClass::class, AbstractClassInterface2::class));

    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, [AbstractClassInterface2::class]));
    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, [
        AbstractClassInterface::class, ParentClassInterface::class,
    ]));
    $this->assertFalse(ClassImplements::hasAll(ParentClass::class, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
    $this->assertFalse(ClassImplements::hasAll(ParentClass::class, SubClassInterface::class, ParentClassInterface::class));
    $this->assertTrue(ClassImplements::hasAll(ParentClass::class, AbstractClassInterface::class, ParentClassInterface::class));
    $this->assertFalse(ClassImplements::hasAll(ParentClass::class, [
        SubClassInterface::class, AbstractClassInterface::class,
    ]));
    $this->assertFalse(ClassImplements::hasAll(AbstractClass::class, [
        SubClassInterface::class, ParentClassInterface::class,
    ]));
});
