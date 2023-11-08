<?php

declare(strict_types=1);

use Yediyuz\Helpers\ArrayHelper;

test('extend', function () {
    $array1 = [
        'one'   => null,
        'nine'  => null,
        'two'   => [],
        'three' => [
            'four'  => 'array1',
            'eight' => null,
        ],
        'seven' => null,
        'ten'   => 'ten-string',
    ];
    $array2 = [
        'one'   => 'array2',
        'nine'  => '',
        'two'   => 'array2-two',
        'seven' => null,
        'ten'   => [],
    ];
    $array3 = [
        'one'   => 'array3',
        'nine'  => 'array3',
        'two'   => null,
        'three' => [
            'five'  => 'array3',
            'eight' => [1],
        ],
        'six'   => null,
        'seven' => null,
    ];

    // expect(ArrayHelper::extend($array1, $array2, $array3))->toEqual([
    expect(ArrayHelper::extend($array1, $array2, $array3))->toEqual([
        'one'   => 'array2',
        'nine'  => '',
        'two'   => [],
        'three' => [
            'four'  => 'array1',
            'five'  => 'array3',
            'eight' => [1],
        ],
        'six'   => null,
        'seven' => null,
        'ten'   => 'ten-string',
    ]);
});

test('replace', function () {
    $array1 = [
        'one'   => null,
        'nine'  => 'array1',
        'two'   => [],
        'three' => [
            'four'  => 'array1',
            'eight' => null,
        ],
        'seven' => null,
    ];
    $array2 = [
        'one'   => 'array2',
        'nine'  => '',
        'two'   => 'array2-two',
        'seven' => null,
    ];
    $array3 = [
        'one'   => 'array3',
        'nine'  => 'array3',
        'two'   => null,
        'three' => [
            'four'  => 'array3',
            'five'  => 'array3',
            'eight' => [1],
        ],
        'six'   => null,
        'seven' => null,
    ];

    expect(ArrayHelper::replace($array1, $array2, $array3))->toEqual([
        'one'   => 'array3',
        'nine'  => 'array3',
        'two'   => null,
        'three' => [
            'four'  => 'array3',
            'five'  => 'array3',
            'eight' => [1],
        ],
        'six'   => null,
        'seven' => null,
    ]);

    $array4 = [
        'one'   => null,
        'two'   => '',
        'three' => 'array4',
    ];
    $array5 = [];

    expect(ArrayHelper::replace($array4, $array5))->toEqual([
        'one'   => null,
        'two'   => '',
        'three' => 'array4',
    ]);

    $array6 = [
        'one'   => null,
        'two'   => '',
        'three' => 'array6',
        'four'  => 1,
    ];
    $array7 = [
        'two'  => ['array7'],
        'four' => ['array7-foo' => 'array7-bar'],
    ];

    expect(ArrayHelper::replace($array6, $array7))->toEqual([
        'one'   => null,
        'two'   => ['array7'],
        'three' => 'array6',
        'four'  => ['array7-foo' => 'array7-bar'],
    ]);

    $config = [
        'token'        => 'foo',
        'repositories' => [
            [
                'name' => 'foo',
            ],
        ],
    ];
    $config2 = [
        'repositories' => [

        ],
    ];

    expect(ArrayHelper::replace($config, $config2))->toEqual([
        'token'        => 'foo',
        'repositories' => [
            [
                'name' => 'foo',
            ],
        ],
    ]);

    $config3 = [
        'token'        => 'foo',
        'repositories' => [
            [
                'name'  => 'foo',
                'child' => [
                    'key'    => 2,
                    'child2' => [
                        'key2' => 2,
                    ],
                ],
            ],
        ],
    ];
    $config4 = [
        'repositories' => [
            [
                'name'  => 'bar',
                'child' => [
                    'key'    => 2,
                    'child2' => [
                        'key2' => null,
                    ],
                ],
            ],
        ],
    ];

    expect(ArrayHelper::replace($config3, $config4))->toEqual([
        'token'        => 'foo',
        'repositories' => [
            [
                'name'  => 'bar',
                'child' => [
                    'key'    => 2,
                    'child2' => [
                        'key2' => null,
                    ],
                ],
            ],
        ],
    ]);
});
