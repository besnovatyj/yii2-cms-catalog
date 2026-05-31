<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\assets;

use yii\web\AssetBundle;

/**
 * Asset Bundle для управления витриной в админке
 */
class ShowcaseAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/media/dist';

    public $js = [
        'js/showcase.js',
    ];

    public $depends = [];
}
