<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\product;

use Besnovatyj\Forms\BaseForm;
use yii\base\Model;
use yii\web\UploadedFile;

class PhotosForm extends BaseForm
{
    /**
     * @var UploadedFile[]
     */
    public array|null $files = null;

    public function rules(): array
    {
        return [
            ['files', 'each', 'rule' => ['image']],
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->files = UploadedFile::getInstances($this, 'files');
            return true;
        }
        return false;
    }
}
