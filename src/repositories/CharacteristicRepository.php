<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\repositories;

use Besnovatyj\Catalog\entities\Characteristic;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class CharacteristicRepository
{
    public function get($id): Characteristic
    {
        if (!$characteristic = Characteristic::findOne($id)) {
            throw new NotFoundException('Characteristic is not found.');
        }
        return $characteristic;
    }

    /**
     * @throws Exception
     */
    public function save(Characteristic $characteristic): void
    {
        if (!$characteristic->save()) {
            throw new RuntimeException('Saving error.');
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(Characteristic $characteristic): void
    {
        if (!$characteristic->delete()) {
            throw new RuntimeException('Removing error.');
        }
    }
}
