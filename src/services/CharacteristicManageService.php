<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\repositories\CharacteristicRepository;
use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\forms\backend\CharacteristicForm;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class CharacteristicManageService
{
    private CharacteristicRepository $characteristics;

    public function __construct(CharacteristicRepository $characteristics)
    {
        $this->characteristics = $characteristics;
    }

    /**
     * @throws Exception
     */
    public function create(CharacteristicForm $form): Characteristic
    {
        $characteristic = Characteristic::create(
            $form->name,
            $form->slug,
            $form->type,
            $form->required,
            $form->default,
            $form->variants,
            $form->sort
        );
        $this->characteristics->save($characteristic);
        return $characteristic;
    }

    /**
     * @throws Exception
     */
    public function edit($id, CharacteristicForm $form): void
    {
        $characteristic = $this->characteristics->get($id);
        $characteristic->edit(
            $form->name,
            $form->slug,
            $form->type,
            $form->required,
            $form->default,
            $form->variants,
            $form->sort
        );
        $this->characteristics->save($characteristic);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove($id): void
    {
        $characteristic = $this->characteristics->get($id);
        $this->characteristics->remove($characteristic);
    }
}
