<?php
    return [
        'module' => [
            [
                'title' => '用户组',
                'icon' => 'fa fa-user',
                'name' => ['user'],
                'subModule' => [
                    [
                        'title' => '用户组',
                        'route' => 'user/catalogue/index'
                    ],
                    [
                        'title' => '用户',
                        'route' => 'user/index'
                    ],
                    [
                        'title' => '权限',
                        'route' => 'permission/index'
                    ]
                ]
            ],
            [
                'title' => '文章',
                'icon' => 'fa fa-file',
                'name' => ['post'],
                'subModule' => [
                    [
                        'title' => '文章组',
                        'route' => 'post/catalogue/index'
                    ],
                    [
                        'title' => '文章',
                        'route' => 'post/index'
                    ]
                ]
            ],
            [
                'title' => '一般的',
                'icon' => 'fa fa-file',
                'name' => ['language'],
                'subModule' => [
                    [
                        'title' => '语言',
                        'route' => 'language/index'
                    ],
                ]
            ]
        ]
    ];
?>