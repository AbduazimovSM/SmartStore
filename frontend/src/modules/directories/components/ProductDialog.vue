<template>
    <Dialog v-model:visible="dialogStore.visible" maximizable :style="{ width: '800px' }" :header="dialogTitle" modal
        class="p-fluid">

        <div class="grid grid-cols-2 gap-2 mt-4">
            <div class="field">
                <FloatLabel variant="on">
                    <InputText id="name" v-model.trim="product.name" autofocus :invalid="submitted && !product.name"
                        fluid />
                    <label for="name">Наименование</label>
                </FloatLabel>
                <small v-if="submitted && !product.name" class="p-error"> Наименование обязательно</small>
            </div>
            <div class="field">
                <FloatLabel variant="on">
                    <InputText id="barcode" v-model.trim="product.barcode" fluid />
                    <label for="barcode">Штрихкод</label>
                </FloatLabel>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div class="field">
                <FloatLabel variant="on">
                    <InputText id="sku" v-model.trim="product.sku" fluid />
                    <label for="sku">Артикул</label>
                </FloatLabel>
            </div>
            <div class="field">
                <FloatLabel variant="on">
                    <AppTreeSelect v-model="product.category_id" :loader="loadCategories" />
                    <label>Категория</label>
                </FloatLabel>
                <small v-if="submitted && !product.category_id" class="p-error"> Категория обязательна</small>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div class="field">
                <FloatLabel variant="on">
                    <AppSelect v-model="product.unit_id" :loader="loadUnits" :show-add="false" />
                    <label>Единица измерения</label>
                </FloatLabel>
                <small v-if="submitted && !product.unit_id" class="p-error"> Единица измерения обязательна</small>
            </div>
            <div class="field">
                <FloatLabel variant="on">
                    <AppSelect v-model="product.brand_id" :loader="loadBrands" :show-add="false" />
                    <label>Бренд</label>
                </FloatLabel>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 mt-8">
            <div class="field">
                <FloatLabel variant="on">
                    <InputNumber id="min_quantity" v-model="product.min_quantity" :min="0" :minFractionDigits="0"
                        :maxFractionDigits="3" fluid />
                    <label for="min_quantity">Минимальный остаток</label>
                </FloatLabel>
            </div>
            <div class="font-medium">
                <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="2000000" :auto="false" customUpload @select="onSelect" chooseLabel="Выбрать" />
            </div>
        </div>

        <div class="field">
            <FloatLabel variant="on" class="mt-8">
                <Textarea id="description" v-model="product.description" rows="3" fluid />
                <label for="description">Описание</label>
            </FloatLabel>
        </div>

        <div class="field">
            <FloatLabel variant="on" class="mt-8">
                <Select id="status" v-model="product.status" :options="statuses" option-label="label"
                    option-value="value" fluid />
                <label for="status">Статус</label>
            </FloatLabel>
        </div>

        <div class="reference-dialog-actions">
            <Button label="Отмена" icon="pi pi-times" text :disabled="saving" @click="dialogStore.close" />

            <Button label="Сохранить" icon="pi pi-check" text :loading="saving" :disabled="saving"
                @click="saveProduct" />
        </div>
    </Dialog>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';

import AppSelect from '@/components/AppSelect.vue';
import AppTreeSelect from '@/components/AppTreeSelect.vue';

import {
    createProduct,
    updateProduct
} from '@/modules/directories/api/product.api';

import {
    getReferences
} from '@/modules/references/api/reference.api';

import {
    useProductDialogStore
} from '@/modules/directories/stores/productDialog.store';

const dialogStore = useProductDialogStore();
const toast = useToast();

const product = ref({});
const submitted = ref(false);
const saving = ref(false);
const imageFile = ref(null);

function onSelect(event) {
    imageFile.value = event.files[0];
}

const statuses = [
    { label: 'Активен', value: true },
    { label: 'Неактивен', value: false }
];

const dialogTitle = computed(() => {
    return product.value.id
        ? 'Редактировать товар'
        : 'Добавить товар';
});

function emptyProduct() {
    return {
        name: '',
        barcode: '',
        sku: '',
        category_id: null,
        unit_id: null,
        brand_id: null,
        image: 'default.png',
        min_quantity: 0,
        description: '',
        status: true
    };
}

async function loadCategories(search = '') {
    const response = await getReferences(
        'category',
        1,
        100,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}

async function loadUnits(search = '') {
    const response = await getReferences(
        'unit',
        1,
        100,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}

async function loadBrands(search = '') {
    const response = await getReferences(
        'brand',
        1,
        100,
        'name',
        'asc',
        search
    );

    return response.data.data.data;
}

watch(
    () => dialogStore.visible,
    (visible) => {
        if (!visible) {
            return;
        }

        submitted.value = false;
        imageFile.value = null;

        if (dialogStore.product) {
            product.value = {
                ...dialogStore.product
            };
        } else {
            product.value = emptyProduct();
        }
    }
);

async function saveProduct() {
    if (saving.value) {
        return;
    }

    submitted.value = true;

    if (
        !product.value.name?.trim() ||
        !product.value.category_id ||
        !product.value.unit_id
    ) {
        return;
    }

    saving.value = true;

    try {
        const formData = new FormData();

        formData.append('name', product.value.name);
        formData.append('barcode', product.value.barcode || '');
        formData.append('sku', product.value.sku || '');
        formData.append('category_id', product.value.category_id);
        formData.append('unit_id', product.value.unit_id);

        if (product.value.brand_id) {
            formData.append('brand_id', product.value.brand_id);
        }

        formData.append(
            'min_quantity',
            product.value.min_quantity ?? 0
        );

        formData.append(
            'description',
            product.value.description || ''
        );

        formData.append(
            'status',
            product.value.status ? 1 : 0
        );

        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }

        let response;

        if (product.value.id) {
            response = await updateProduct(
                product.value.id,
                formData
            );
        } else {
            response = await createProduct(
                formData
            );
        }

        toast.add({
            severity: 'success',
            summary: 'Успешно',
            detail: response.data.message,
            life: 3000
        });

        dialogStore.saved();
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Ошибка',
            detail:
                error.response?.data?.message ||
                'Не удалось сохранить товар',
            life: 3000
        });
    } finally {
        saving.value = false;
    }
}
</script>