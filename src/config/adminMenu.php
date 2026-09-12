<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [
    // Products
    [
        'label'     => 'Products',
        'iconClass' => 'bi bi-box-seam me-1',
        'url' => ['/Catalog/backend/product/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Catalog/backend/product');
        },
        '_meta' => [
            'placements' => [
                [
                    'location'      => 'left-sidebar',
                    'group'         => 'Catalog',
                    'groupIcon'     => 'bi bi-journals',
                    'priority'      => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Brands
    [
        'label'     => 'Brands',
        'iconClass' => 'bi bi-bookmark-star me-1',
        'url' => ['/Catalog/backend/brand/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Catalog/backend/brand');
        },
        '_meta' => [
            'placements' => [
                [
                    'location'      => 'left-sidebar',
                    'group'         => 'Catalog',
                    'groupIcon'     => 'bi bi-journals',
                    'priority'      => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Categories
    [
        'label'     => 'Categories',
        'iconClass' => 'bi bi-diagram-3 me-1',
        'url' => ['/Catalog/backend/category/index'],
        'active' => static function () {
            return \Yii::$app->request->url === '/Catalog/backend/category/index';
        },
        '_meta' => [
            'placements' => [
                [
                    'location'      => 'left-sidebar',
                    'group'         => 'Catalog',
                    'groupIcon'     => 'bi bi-journals',
                    'priority'      => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Categories-list
    [
        'label'     => 'Categories-list',
        'iconClass' => 'bi bi-diagram-3 me-1',
        'url' => ['/Catalog/backend/category-simple/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Catalog/backend/category-simple');
        },
        '_meta' => [
            'placements' => [
                [
                    'location'      => 'left-sidebar',
                    'group'         => 'Catalog',
                    'groupIcon'     => 'bi bi-journals',
                    'priority'      => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Showcases
    [
        'label' => 'Showcases',
        'iconClass' => 'bi bi-easel me-1',
        'url' => ['/Catalog/backend/showcase/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Catalog/backend/showcase');
        },
        '_meta' => [
            'placements' => [
                [
                    'location'      => 'left-sidebar',
                    'group'         => 'Catalog',
                    'groupIcon'     => 'bi bi-journals',
                    'priority'      => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

];
