<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\helpers\ValueHelper;
use Besnovatyj\Catalog\widgets\products\ProductsArrayWidget;
use yii\helpers\Url;

?>

<div class="swiper-wrapper">
    <?php $productsWidget = ProductsArrayWidget::begin(); ?>
    <?php foreach ($productsWidget->products as $itemId => $item): ?>

        <?php
        $productPhotoThumbUrl = isset($item->photos[2]) ? $item->photos[2]->getThumbFileUrl('file', 'slider') : '';
        $productPhotoThumbUrl_lazy = isset($item->photos[2]) ? $item->photos[2]->getThumbFileUrl('file', 'slider_lazy') : '';
        $productPhotoFileUrl = isset($item->photos[2]) ? $item->photos[2]->getImageFileUrl('file') : '';
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
                    <a href="<?= Url::to(['/catalog/product/item', 'id' => $item->id]) ?>"
                       class="text-color black-75">
                        <i title="Перейти на страницу"></i>
                    </a>
                </div>
                <div class="card has-image parent">
                    <img data-src="<?= $productPhotoThumbUrl ?>"
                         src="<?= $productPhotoThumbUrl_lazy ?>"
                         alt="<?= $item->name_short ?>">
                    <div>
                        <p>
                            <a href="<?= Url::to(['/catalog/product/item', 'id' => $item->id]) ?>">
                                <span><?= $item->name_short ?></span>
                            </a>
                            <span>
                                <?= ValueHelper::getValue($item->values, 'package_weight_volume') . ' ' . ValueHelper::getValue($item->values, 'unit_change_packaging') ?>

                                <?php
                                if ((bool)ValueHelper::getValue($item->values, 'show_the_number_of_pieces_in_the_catalog_grid') === true) {
                                    echo '/ <small>' . ValueHelper::getValue($item->values, 'units_per_one_package') . ' ' . ValueHelper::getValue($item->values, 'unit_of_measurement_of_number_of_pieces') . '</small>';
                                }
                                ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php ProductsArrayWidget::end(); ?>
</div>
