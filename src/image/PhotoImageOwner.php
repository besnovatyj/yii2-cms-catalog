<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\image;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\product\Photo;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Images\contracts\ImageOwnerInterface;
use yii\db\Exception;

/**
 * Адаптер Catalog к ImageOwnerInterface.
 *
 * Реализует pessimistic lock через PessimisticLockBehavior Product,
 * чтобы исключить race condition при параллельной загрузке изображений
 * (несколько запросов одновременно видят main_image_id = null и пытаются
 * его установить, что приводит к FK constraint violation).
 */
readonly class PhotoImageOwner implements ImageOwnerInterface
{
    public function __construct(
        private Product           $product,
        private ProductRepository $repository,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getOwnerId(): int
    {
        return $this->product->id;
    }

    /**
     * {@inheritdoc}
     *
     * @return Photo[]
     */
    public function getOwnedImages(): array
    {
        return $this->product->photos;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainImageId(): ?int
    {
        return $this->product->main_photo_id ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainImageId(?int $imageId): void
    {
        $this->product->setMainPhoto($imageId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function saveOwner(): void
    {
        $this->repository->save($this->product);
    }

    /**
     * Блокирует строку галереи (SELECT FOR UPDATE) до конца транзакции.
     *
     * Исключает race condition при параллельной загрузке нескольких файлов.
     * @throws Exception
     */
    public function lockOwner(): void
    {
        $this->product->lock();
    }

    /**
     * Обновляет данные галереи из БД после применения блокировки.
     */
    public function refreshOwner(): void
    {
        $this->product->refresh();
    }
}
