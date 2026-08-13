<template>
    <div class="card">
        <ProductMenu class="mb-8" />

        <Toolbar style="border-radius: 0">
            <template #start>
                <Button
                    label="Добавить"
                    icon="pi pi-plus"
                    class="mr-2"
                    @click="addProduct"
                />

                <Button
                    icon="pi pi-trash"
                    severity="danger"
                    :disabled="!selectedProducts.length"
                    @click="confirmDeleteProducts"
                />
            </template>

            <template #end>
                <IconField iconPosition="left">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>

                    <InputText
                        v-model="search"
                        placeholder="Search..."
                    />
                </IconField>
            </template>
        </Toolbar>

        <div class="table-wrapper">
            <DataTable
                class="base-table text-center"
                v-model:selection="selectedProducts"
                :value="products"
                :loading="loading"
                showGridlines
                data-key="id"
                resizableColumns
                columnResizeMode="fit"
                scrollable
                scrollHeight="flex"
            >
                <template #empty>
                    <p class="text-center">
                        Данные не найдены
                    </p>
                </template>

                <Column
                    selection-mode="multiple"
                    style="width: 3rem"
                    :exportable="false"
                />

                <Column field="id" header="ID" sortable />

                <Column field="name" header="Наименование" sortable />

                <Column field="category_id" header="Категория" />

                <Column field="unit_id" header="Ед. измерения" />

                <Column field="brand_id" header="Бренд" />

                <Column field="status" header="Статус">
                    <template #body="{ data }">
                        <Tag
                            :value="data.status ? 'Активен' : 'Неактивен'"
                            :severity="data.status ? 'success' : 'danger'"
                        />
                    </template>
                </Column>

                <Column
                    style="width: 4rem"
                    :exportable="false"
                >
                    <template #body="{ data }">
                        <Button
                            icon="pi pi-ellipsis-h"
                            rounded
                            text
                            severity="secondary"
                            @click="openActionsMenu($event, data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

import ProductMenu from '@/modules/directories/components/ProductMenu.vue';

const products = ref([]);
const selectedProducts = ref([]);

const loading = ref(false);
const search = ref('');

function addProduct() {
    console.log('Добавить товар');
}

function confirmDeleteProducts() {
    console.log('Удалить выбранные');
}

function openActionsMenu(event, product) {
    console.log(product);
}
</script>