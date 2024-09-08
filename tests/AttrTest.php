<?php

declare(strict_types=1);

use Yediyuz\Helpers\Attr;

test('attributes', function () {
    $attr = new class extends Attr {
        public function getBazAttribute(): string
        {
            return 'baz';
        }
    };
    $attr = new $attr(['foo' => 'one', 'bar' => 'two']);

    $this->assertSame('one', $attr->foo);
    $this->assertSame('two', $attr->bar);
    $this->assertSame('baz', $attr->baz);

    $attr->bar = 'yediyuz';
    $attr->yediyuz = 'helpers';
    $attr->offsetSet('yediyuz', 'helpers');

    $this->assertSame('helpers', $attr->yediyuz);
    $this->assertSame('yediyuz', $attr->bar);
    $attr->offsetUnset('yediyuz', 'helpers');
    $this->assertNull($attr->yediyuz);

    $this->assertTrue(isset($attr['foo']));
    $this->assertSame($attr['foo'], 'one');
    $this->assertTrue(isset($attr->foo));
    $this->assertFalse(isset($attr->doclara));

    expect($attr->toArray())->toEqual([
        'foo' => 'one',
        'bar' => 'yediyuz',
    ]);

    unset($attr->foo);

    $this->assertFalse(isset($attr->foo));

    expect((string) $attr)
        ->toBeJson()
        ->and($attr->jsonSerialize())->toBeArray()
        ->and($attr->toJson())->toBeJson();

});
