<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use yii\helpers\Url;

/* @var $items ShowcaseItem[] */

?>

<div class="swiper-wrapper">
    <?php foreach ($items as $itemId => $showcaseItem): ?>

        <?php
        $product = $showcaseItem->product;
        $displayPhoto = $showcaseItem->getDisplayPhoto();

        $productPhotoThumbUrl = $displayPhoto ? $displayPhoto->getThumbUrl('file', 'slider') : '';
        $productPhotoThumbUrl_lazy = $displayPhoto ? $displayPhoto->getThumbUrl('file', 'slider_lazy') : '';
        $productPhotoFileUrl = $displayPhoto ? $displayPhoto->getUploadUrl('file') : '';

        $displayTitle = $showcaseItem->getDisplayTitle();
        $displayDescription = $showcaseItem->getDisplayDescription();
        ?>

        <?php
        // Для первых четырёх слайдов добавляем эффект
        $AosData = match ($itemId) {
            0 => 'data-aos="fade-up" data-aos-delay="800" data-aos-anchor="home"',
            1 => 'data-aos="fade-up" data-aos-delay="1000" data-aos-anchor="home"',
            2 => 'data-aos="fade-up" data-aos-delay="1200" data-aos-anchor="home"',
            3 => 'data-aos="fade-up" data-aos-delay="1400" data-aos-anchor="home"',
            default => '',
        };
        ?>
        <div class="swiper-slide" <?= $AosData ?> >
            <div class="item">
                <div class="gallery-icon">
                    <a href=" <?= $productPhotoFileUrl ?>"
                       class="lightbox-link text-color black-75">
                        <i class="fa-solid fa-up-right-and-down-left-from-center"
                           title="Увеличить изображение"></i>
                    </a>
                    <a href="<?= Url::to(['/Catalog/product/item', 'id' => $product->id]) ?>"
                       class="text-color black-75">
                        <i title="Перейти на страницу"></i>
                    </a>
                </div>
                <div class="card has-image parent">
                    <img data-src="<?= $productPhotoThumbUrl ?>"
                         src="<?= $productPhotoThumbUrl_lazy ?>"
                         alt="<?= $displayTitle ?>">
                    <div>
                        <p>
                            <a href="<?= Url::to(['/Catalog/product/item', 'id' => $product->id]) ?>">
                                <span><?= $displayTitle ?></span>
                            </a>
                            <?php if ($displayDescription !== ''): ?>
                                <span><?= $displayDescription ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
