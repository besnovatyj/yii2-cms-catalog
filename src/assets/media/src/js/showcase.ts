/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/**
 * Showcase Manager — управление элементами витрины товаров
 *
 * Drag-and-drop сортировка, добавление/удаление товаров,
 * настройка фото, характеристик и кастомных заголовков.
 */

interface ShowcaseConfig {
    showcaseId: number;
    endpoints: {
        addItem: string;
        removeItem: string;
        reorderItems: string;
        configureItem: string;
        toggleItemStatus: string;
        productPhotos: string;
    };
    csrfToken: string;
    csrfParam: string;
    characteristics: Array<{ slug: string; name: string }>;
}

interface ShowcaseItemData {
    id: number;
    product_id: number;
    product_name: string;
    product_name_short: string;
    photo_index: number | null;
    display_characteristics: string[];
    custom_title: string | null;
    sort: number;
    status: number;
    photos: Array<{ index: number; thumb: string; isMain: boolean }>;
}

interface ApiResponse {
    status: 'success' | 'error';
    message?: string;
    item?: ShowcaseItemData;
    itemStatus?: number;
    errors?: Record<string, string[]>;
}

class ShowcaseManager {
    private readonly config: ShowcaseConfig;
    private readonly itemsList: HTMLElement;
    private readonly productSelect: HTMLSelectElement;
    private readonly addBtn: HTMLElement;
    private readonly saveOrderBtn: HTMLElement;
    private readonly emptyMessage: HTMLElement | null;
    private readonly itemTemplate: HTMLTemplateElement;
    private draggedItem: HTMLElement | null = null;
    private orderChanged = false;

    constructor(config: ShowcaseConfig) {
        this.config = config;

        this.itemsList = document.getElementById('showcase-items-list')!;
        this.productSelect = document.getElementById('showcase-product-select') as HTMLSelectElement;
        this.addBtn = document.getElementById('showcase-add-item-btn')!;
        this.saveOrderBtn = document.getElementById('showcase-save-order-btn')!;
        this.emptyMessage = document.getElementById('showcase-empty-message');
        this.itemTemplate = document.getElementById('showcase-item-template') as HTMLTemplateElement;

        this.bindEvents();
        this.initDragAndDrop();
    }

    /**
     * Привязка событий
     */
    private bindEvents(): void {
        // Добавление товара
        this.addBtn.addEventListener('click', () => this.addItem());

        // Сохранение порядка
        this.saveOrderBtn.addEventListener('click', () => this.saveOrder());

        // Делегирование событий для элементов витрины
        this.itemsList.addEventListener('click', (e: Event) => {
            const target = e.target as HTMLElement;
            const btn = target.closest('button');
            if (!btn) return;

            const itemEl = btn.closest('.showcase-item') as HTMLElement;
            if (!itemEl) return;

            const itemId = parseInt(itemEl.dataset.itemId || '0', 10);

            if (btn.classList.contains('showcase-item-remove')) {
                this.removeItem(itemId, itemEl);
            } else if (btn.classList.contains('showcase-item-configure')) {
                this.toggleSettings(itemEl);
            } else if (btn.classList.contains('showcase-item-toggle-status')) {
                this.toggleItemStatus(itemId, itemEl);
            } else if (btn.classList.contains('showcase-item-save-config')) {
                this.saveItemConfig(itemId, itemEl);
            }
        });
    }

    /**
     * Инициализация drag-and-drop через нативный HTML5 API
     */
    private initDragAndDrop(): void {
        this.itemsList.addEventListener('dragstart', (e: DragEvent) => {
            const item = (e.target as HTMLElement).closest('.showcase-item') as HTMLElement;
            if (!item) return;
            this.draggedItem = item;
            item.classList.add('opacity-50');
            e.dataTransfer!.effectAllowed = 'move';
        });

        this.itemsList.addEventListener('dragend', () => {
            if (this.draggedItem) {
                this.draggedItem.classList.remove('opacity-50');
                this.draggedItem = null;
            }
        });

        this.itemsList.addEventListener('dragover', (e: DragEvent) => {
            e.preventDefault();
            e.dataTransfer!.dropEffect = 'move';

            const target = (e.target as HTMLElement).closest('.showcase-item') as HTMLElement;
            if (!target || target === this.draggedItem) return;

            const rect = target.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;

            if (e.clientY < midY) {
                this.itemsList.insertBefore(this.draggedItem!, target);
            } else {
                this.itemsList.insertBefore(this.draggedItem!, target.nextSibling);
            }
        });

        this.itemsList.addEventListener('drop', (e: DragEvent) => {
            e.preventDefault();
            this.orderChanged = true;
            this.saveOrderBtn.style.display = '';
        });

        // Делаем элементы перетаскиваемыми через handle
        this.makeDraggable();
    }

    /**
     * Назначить draggable всем элементам витрины
     */
    private makeDraggable(): void {
        const items = this.itemsList.querySelectorAll('.showcase-item');
        items.forEach((item) => {
            (item as HTMLElement).draggable = true;

            // Drag только за handle
            const handle = item.querySelector('.showcase-item-handle') as HTMLElement;
            if (handle) {
                (item as HTMLElement).draggable = false;
                handle.addEventListener('mousedown', () => {
                    (item as HTMLElement).draggable = true;
                });
                handle.addEventListener('mouseup', () => {
                    (item as HTMLElement).draggable = false;
                });
            }
        });
    }

    /**
     * Добавить товар в витрину
     */
    private async addItem(): Promise<void> {
        const productId = parseInt(this.productSelect.value, 10);
        if (!productId) return;

        this.addBtn.setAttribute('disabled', 'true');

        try {
            const response = await this.request<ApiResponse>(this.config.endpoints.addItem, {
                showcase_id: this.config.showcaseId,
                product_id: productId,
            });

            if (response.status === 'success' && response.item) {
                this.renderNewItem(response.item);
                this.removeProductFromSelect(productId);
                this.hideEmptyMessage();
                this.updateItemsCount(1);
            } else {
                alert(response.message || 'Ошибка добавления');
            }
        } catch (err) {
            alert('Ошибка сети');
        } finally {
            this.addBtn.removeAttribute('disabled');
        }
    }

    /**
     * Удалить элемент из витрины
     */
    private async removeItem(itemId: number, itemEl: HTMLElement): Promise<void> {
        if (!confirm('Удалить товар из витрины?')) return;

        try {
            const response = await this.request<ApiResponse>(this.config.endpoints.removeItem, {
                item_id: itemId,
            });

            if (response.status === 'success') {
                // Вернуть товар в выпадающий список
                const config = JSON.parse(itemEl.dataset.itemConfig || '{}');
                const productName = itemEl.querySelector('strong')?.textContent || '';
                if (config.product_id && productName) {
                    this.addProductToSelect(config.product_id, productName);
                }

                itemEl.remove();
                this.updateItemsCount(-1);

                if (!this.itemsList.querySelector('.showcase-item')) {
                    this.showEmptyMessage();
                }
            } else {
                alert(response.message || 'Ошибка удаления');
            }
        } catch (err) {
            alert('Ошибка сети');
        }
    }

    /**
     * Показать/скрыть панель настроек элемента
     */
    private toggleSettings(itemEl: HTMLElement): void {
        const settings = itemEl.querySelector('.showcase-item-settings') as HTMLElement;
        if (settings) {
            settings.style.display = settings.style.display === 'none' ? '' : 'none';
        }
    }

    /**
     * Переключить статус элемента
     */
    private async toggleItemStatus(itemId: number, itemEl: HTMLElement): Promise<void> {
        try {
            const response = await this.request<ApiResponse>(this.config.endpoints.toggleItemStatus, {
                item_id: itemId,
            });

            if (response.status === 'success') {
                const statusEl = itemEl.querySelector('.showcase-item-status');
                if (statusEl) {
                    const isActive = response.itemStatus === 1;
                    statusEl.innerHTML = isActive
                        ? '<span class="badge bg-success">Вкл</span>'
                        : '<span class="badge bg-secondary">Выкл</span>';
                }
            } else {
                alert(response.message || 'Ошибка');
            }
        } catch (err) {
            alert('Ошибка сети');
        }
    }

    /**
     * Сохранить настройки элемента (фото, характеристики, заголовок)
     */
    private async saveItemConfig(itemId: number, itemEl: HTMLElement): Promise<void> {
        // Собрать photo_index
        const photoRadio = itemEl.querySelector('.showcase-photo-radio:checked') as HTMLInputElement | null;
        const photoIndex = photoRadio?.value || null;

        // Собрать характеристики
        const charCheckboxes = itemEl.querySelectorAll('.showcase-characteristic-checkbox:checked');
        const displayCharacteristics: string[] = [];
        charCheckboxes.forEach((cb) => {
            displayCharacteristics.push((cb as HTMLInputElement).value);
        });

        // Собрать кастомный заголовок
        const titleInput = itemEl.querySelector('.showcase-item-custom-title') as HTMLInputElement;
        const customTitle = titleInput?.value || null;

        try {
            const response = await this.request<ApiResponse>(this.config.endpoints.configureItem, {
                item_id: itemId,
                photo_index: photoIndex,
                display_characteristics: displayCharacteristics,
                custom_title: customTitle,
            });

            if (response.status === 'success') {
                // Обновить превью (перезагрузить страницу для простоты)
                window.location.reload();
            } else {
                alert(response.message || 'Ошибка сохранения');
            }
        } catch (err) {
            alert('Ошибка сети');
        }
    }

    /**
     * Сохранить новый порядок элементов
     */
    private async saveOrder(): Promise<void> {
        const items = this.itemsList.querySelectorAll('.showcase-item');
        const sortData: Record<string, number> = {};

        items.forEach((item, index) => {
            const itemId = (item as HTMLElement).dataset.itemId;
            if (itemId) {
                sortData[itemId] = index + 1;
            }
        });

        this.saveOrderBtn.setAttribute('disabled', 'true');

        try {
            const response = await this.request<ApiResponse>(this.config.endpoints.reorderItems, {
                sort_data: sortData,
            });

            if (response.status === 'success') {
                this.orderChanged = false;
                this.saveOrderBtn.style.display = 'none';
            } else {
                alert(response.message || 'Ошибка сортировки');
            }
        } catch (err) {
            alert('Ошибка сети');
        } finally {
            this.saveOrderBtn.removeAttribute('disabled');
        }
    }

    /**
     * Рендеринг нового элемента витрины из шаблона
     */
    private renderNewItem(item: ShowcaseItemData): void {
        const template = this.itemTemplate.content.cloneNode(true) as DocumentFragment;
        const el = template.querySelector('.showcase-item') as HTMLElement;

        if (el) {
            el.dataset.itemId = String(item.id);
            el.dataset.itemConfig = JSON.stringify({
                id: item.id,
                product_id: item.product_id,
                photo_index: item.photo_index,
                display_characteristics: item.display_characteristics,
                custom_title: item.custom_title,
                status: item.status,
            });

            const nameEl = el.querySelector('strong');
            if (nameEl) {
                nameEl.textContent = item.product_name;
            }

            this.itemsList.appendChild(el);
            this.makeDraggable();
        }
    }

    /**
     * Удалить товар из выпадающего списка
     */
    private removeProductFromSelect(productId: number): void {
        const option = this.productSelect.querySelector(`option[value="${productId}"]`);
        if (option) {
            option.remove();
        }
        this.productSelect.value = '';
    }

    /**
     * Добавить товар обратно в выпадающий список
     */
    private addProductToSelect(productId: number, productName: string): void {
        const option = document.createElement('option');
        option.value = String(productId);
        option.textContent = productName;
        this.productSelect.appendChild(option);
    }

    private hideEmptyMessage(): void {
        if (this.emptyMessage) {
            this.emptyMessage.style.display = 'none';
        }
    }

    private showEmptyMessage(): void {
        if (this.emptyMessage) {
            this.emptyMessage.style.display = '';
        }
    }

    private updateItemsCount(delta: number): void {
        const countEl = document.getElementById('showcase-items-count');
        if (countEl) {
            const current = parseInt(countEl.textContent || '0', 10);
            countEl.textContent = String(current + delta);
        }
    }

    /**
     * AJAX-запрос с CSRF-токеном
     */
    private async request<T>(url: string, data: Record<string, unknown>): Promise<T> {
        const formData = new FormData();
        formData.append(this.config.csrfParam, this.config.csrfToken);

        for (const [key, value] of Object.entries(data)) {
            if (Array.isArray(value)) {
                value.forEach((v, i) => {
                    formData.append(`${key}[${i}]`, String(v));
                });
            } else if (typeof value === 'object' && value !== null) {
                for (const [k, v] of Object.entries(value as Record<string, unknown>)) {
                    formData.append(`${key}[${k}]`, String(v));
                }
            } else if (value !== null && value !== undefined) {
                formData.append(key, String(value));
            }
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        return response.json() as Promise<T>;
    }
}

// --- Инициализация ---
document.addEventListener('DOMContentLoaded', () => {
    const config = (window as unknown as { showcaseConfig?: ShowcaseConfig }).showcaseConfig;
    if (config) {
        new ShowcaseManager(config);
    }
});
