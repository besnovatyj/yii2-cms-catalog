Реализация витрины (Showcase)

Созданные файлы

Сущности:

- `src/entities/showcase/Showcase.php` — витрина (code, name, status, sort)
- `src/entities/showcase/ShowcaseItem.php` — элемент витрины (product_id, photo_index, display_characteristics JSON,
  custom_title, sort, status) + методы `getDisplayPhoto()`, `getDisplayTitle()`, `getDisplayCharacteristicSlugs() `

Миграции:

- `src/migrations/m250428_123040_create_catalog_showcases_table.php`
- `src/migrations/m250428_123050_create_catalog_showcase_items_table.php`
- Обновлена `m250428_123030_create_catalog_foreign_key_constraints.php` — FK для showcase_items → showcases (CASCADE) и
  showcase_items → products (CASCADE)

Backend:

- `src/repositories/ShowcaseRepository.php`
- `src/services/ShowcaseManageService.php` — create, edit, activate/draft, remove, addItem, configureItem, removeItem,
  reorderItems, toggleItemStatus
- `src/forms/backend/showcase/ShowcaseForm.php`
- `src/forms/backend/showcase/ShowcaseItemForm.php` - src/controllers/backend/ShowcaseController.php — полный CRUD +
  AJAX endpoints для элементов
- `src/helpers/ShowcaseHelper.php`
- `src/views/backend/showcase/` — index, create, update, _form, view (управление элементами), _showcase_item, 
  _showcase_item_template

TypeScript:

- `src/assets/media/` — package.json, tsconfig.json, esbuild.config.js
- `src/assets/media/src/js/showcase.ts` — drag-and-drop сортировка, AJAX добавление/удаление/настройка элементов, выбор
  фото, характеристик, кастомного заголовка
- `src/assets/ShowcaseAsset.php` — AssetBundle

Frontend:

- `src/readModels/ShowcaseReadRepository.php` — findByCode(), getItemsByCode()
- `src/widgets/showcase/ShowcaseWidget.php` — виджет с DI
- `src/widgets/showcase/views/default.php` — view, полностью заменяющий хардкод из `index_from_theme.php`

Конфигурация:

- Обновлён `src/config/adminMenu.php` — добавлен пункт "Showcases"

Как использовать

В теме (фронтенд) — вместо хардкода в `index_from_theme.php`:
`<?= \Besnovatyj\Catalog\widgets\showcase\ShowcaseWidget::widget(['code' => 'homepage-slider']) ?>`

В админке:

1. Catalog → Showcases → Создать витрину (код: `homepage-slider`)
2. На странице витрины — выбрать товары из выпадающего списка
3. Для каждого товара через кнопку "шестерёнка" настроить: какое фото показывать, какие характеристики выводить,
   кастомный заголовок
4. Drag-and-drop для сортировки
5. Activate когда готово

Сборка
TypeScript:                                                                                                                                                                                       
`cd app/packages/besnovatyj/yii2-cms-catalog/src/assets/media
npm
install                                                                                                                                                                                              
npm run build`                       
