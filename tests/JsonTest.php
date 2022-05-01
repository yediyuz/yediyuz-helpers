<?php

use Yediyuz\Helpers\Exceptions\JsonException;
use Yediyuz\Helpers\Json;

beforeEach(function () {
    $this->badData = urldecode('bad utf string %C4_');
});

test('decode - failures', function () {
    Json::decode('');
})->throws(JsonException::class, 'Syntax error');

test('encode - failures', function () {
    Json::encode($this->badData);
})->throws(JsonException::class, 'Malformed UTF-8 characters');

test('encode - pass', function () {
    expect(Json::encode(['foo']))
        ->toBeJson()
        ->toEqual('["foo"]');

    $json = <<<'JSON'
    [
        "foo"
    ]
    JSON;

    expect(Json::encode(['foo'], JSON_PRETTY_PRINT))
        ->toBeJson()
        ->toEqual($json)
        ->and(Json::encode(['foo']))
        ->not
        ->toEqual($json);

    expect(Json::encode(collect(['foo'])))
        ->toBeJson()
        ->toEqual('["foo"]');

    expect(Json::encode(collect(['foo' => collect(['bar'])])))
        ->toBeJson()
        ->toEqual('{"foo":["bar"]}');

    expect(Json::encode(collect(['foo' => 1])))
        ->toBeJson()
        ->toEqual('{"foo":1}');

    expect(Json::encode(1))
        ->toBeJson()
        ->toEqual('1');
});

test('decode - pass', function () {
    $expectedObject = new stdClass();
    $expectedObject->foo = 'bar';

    expect(Json::decode('{"foo":"bar"}'))
        ->toBeObject()
        ->toEqual($expectedObject);

    expect(Json::decode('{"foo":"bar"}', true))
        ->toEqual(['foo' => 'bar']);
});

test('decodeOr', function () {
    $expectedObject = new stdClass();
    $expectedObject->baz = 'foo';

    expect(Json::decodeOr('{"baz":"foo"}', true, fn () => ['foo' => 'bar']))->toEqual(['baz' => 'foo']);
    expect(Json::decodeOr('{"baz":"foo"}', true, fn () => null))->toEqual(['baz' => 'foo']);
    expect(Json::decodeOr('{"baz":"foo"}', true, fn () => 'foobar'))->toEqual(['baz' => 'foo']);
    expect(Json::decodeOr('{"baz":"foo"}', false, fn () => 'foo'))->toEqual($expectedObject);
    expect(Json::decodeOr('{"baz":"foo"}', false, fn () => null))->toEqual($expectedObject);

    expect(Json::decodeOr('', true, fn () => ['foo' => 'bar']))->toEqual(['foo' => 'bar']);
    expect(Json::decodeOr('', true, fn () => null))->toBeNull();
    expect(Json::decodeOr('', true, fn () => 'foobar'))->toEqual('foobar');
    expect(Json::decodeOr('', false, fn () => 'foo'))->toEqual('foo');
    expect(Json::decodeOr('', false, fn () => null))->toBeNull();
    expect(Json::decodeOr('', true, 'foobar'))->toEqual('foobar');
    expect(Json::decodeOr('', false, 'foobar'))->toEqual('foobar');
    expect(Json::decodeOr('', false, null))->toBeNull();
    expect(Json::decodeOr(null, false, 'foo'))->toEqual('foo');
    expect(Json::decodeOr(null, true, 'foo'))->toEqual('foo');
    expect(Json::decodeOr('[]', true, 'foo'))->toEqual([]);
});

test('encodeOr', function () {
    expect(Json::encodeOr(['baz' => 'foo'], fn () => ['foo' => 'bar']))->toBeJson()->toEqual('{"baz":"foo"}');
    expect(Json::encodeOr(['baz' => 'foo'], fn () => null))->toBeJson()->toEqual('{"baz":"foo"}');
    expect(Json::encodeOr(['baz' => 'foo'], fn () => 'foobar'))->toBeJson()->toEqual('{"baz":"foo"}');

    expect(Json::encodeOr($this->badData, fn () => ['foo' => 'bar']))->toEqual(['foo' => 'bar']);
    expect(Json::encodeOr($this->badData, fn () => '{"foo":"bar"}'))->toBeJson()->toEqual('{"foo":"bar"}');
    expect(Json::encodeOr($this->badData, fn () => Json::encode(['foo' => 'bar'])))->toBeJson()->toEqual('{"foo":"bar"}');
    expect(Json::encodeOr($this->badData, fn () => Json::encodeOr($this->badData, 'bar')))->toEqual('bar');
    expect(Json::encodeOr($this->badData, fn () => null))->toBeNull();
    expect(Json::encodeOr($this->badData, fn () => 'foobar'))->toEqual('foobar');
    expect(Json::encodeOr($this->badData, fn () => 'foo'))->toEqual('foo');
    expect(Json::encodeOr($this->badData, fn () => null))->toBeNull();
    expect(Json::encodeOr($this->badData, 'foobar'))->toEqual('foobar');
    expect(Json::encodeOr($this->badData, 'foobar'))->toEqual('foobar');
    expect(Json::encodeOr($this->badData, null))->toBeNull();
});

test('encodeOrFail - pass', function () {
    expect(Json::encodeOrFail(['foo']))->toBeJson()->toEqual('["foo"]');
});

test('decodeOrFail - pass', function () {
    expect(Json::decodeOrFail('["foo"]', true))->toEqual(['foo']);
});

test('decodeOrFail - default exception', function () {
    Json::decodeOrFail('', true);
})->throws(JsonException::class, 'Syntax error');

test('encodeOrFail - default exception', function () {
    Json::encodeOrFail($this->badData);
})->throws(JsonException::class, 'Malformed UTF-8 characters');

test('decodeOrFail - custom exception as an class-string', function () {
    Json::decodeOrFail('', true, Exception::class);
})->throws(Exception::class, 'Syntax error');

test('encodeOrFail - custom exception as an class-string', function () {
    Json::encodeOrFail($this->badData, 0, Exception::class);
})->throws(Exception::class, 'Malformed UTF-8 characters');

test('decodeOrFail - custom exception as an array', function () {
    Json::decodeOrFail('', true, [Exception::class]);
})->throws(Exception::class, 'Syntax error');

test('encodeOrFail - custom exception as an array', function () {
    Json::encodeOrFail($this->badData, 0, [Exception::class]);
})->throws(Exception::class, 'Malformed UTF-8 characters');

test('decodeOrFail - custom exception as an array with message', function () {
    Json::decodeOrFail('', true, [Exception::class, 'foo message']);
})->throws(Exception::class, 'foo message');

test('encodeOrFail - custom exception as an array with message', function () {
    Json::encodeOrFail($this->badData, 0, [Exception::class, 'foo message']);
})->throws(Exception::class, 'foo message');

test('decodeOrFail - custom exception as an exception', function () {
    Json::decodeOrFail('', true, new Exception('foo bar'));
})->throws(Exception::class, 'foo bar');

test('encodeOrFail - custom exception as an exception', function () {
    Json::encodeOrFail($this->badData, 0, new Exception('foo bar'));
})->throws(Exception::class, 'foo bar');
