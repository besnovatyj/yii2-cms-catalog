<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\helpers;

use Besnovatyj\Catalog\entities\Characteristic;
use Exception;
use yii\helpers\ArrayHelper;

class CharacteristicHelper
{
    public static function typeList(): array
    {
        return [
            Characteristic::TYPE_STRING => 'String',
            Characteristic::TYPE_INTEGER => 'Integer number',
            Characteristic::TYPE_FLOAT => 'Float number',
        ];
    }

    /**
     * @throws Exception
     */
    public static function typeName($type): string
    {
        return ArrayHelper::getValue(self::typeList(), $type);
    }
}
