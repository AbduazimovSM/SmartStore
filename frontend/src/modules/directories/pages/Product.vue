<template>
    <div class="card universal-page-card">
        <ProductMenu class="mb-8" />

        <Toolbar style="border-radius: 0">
            <template #start>
                <Button :label="t('global.buttons.add')" icon="pi pi-plus" class="mr-2" @click="addProduct"/>

                <Button icon="pi pi-trash" :disabled="!selectedProducts.length" severity="danger"
                    @click="confirmDeleteProducts">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-trash" viewBox="0 0 16 16">
                        <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                        <path
                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                    </svg>
                </Button>
            </template>

            <template #end>
                <IconField iconPosition="left">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>

                    <InputText v-model="search" :placeholder="t('global.buttons.search')" @input="onSearch" />
                </IconField>
            </template>
        </Toolbar>

        <div class="table-wrapper">
            <DataTable class="base-table" v-model:selection="selectedProducts" :value="products" :loading="loading"
                showGridlines data-key="id" resizableColumns columnResizeMode="fit" scrollable scrollHeight="flex"
                :tableStyle="{ minWidth: '760px' }" lazy :sortField="sortField"
                :sortOrder="sortOrder === 'asc' ? 1 : -1" @sort="onSort">
                <template #empty>
                    <p class="text-center">
                        {{ t('global.messages.no_data') }}
                    </p>
                </template>

                <Column selection-mode="multiple" style="width: 3rem" :exportable="false" />

                <Column field="id" :header="t('directories.products.table.id')" sortable />

                <Column field="name" :header="t('directories.products.table.name')" sortable />

                <Column field="barcode" :header="t('directories.products.table.barcode')" sortable />

                <Column field="sku" :header="t('directories.products.table.sku')" sortable />

                <Column :header="t('directories.products.table.category')">
                    <template #body="{ data }">
                        {{ data.category?.name || '' }}
                    </template>
                </Column>

                <Column :header="t('directories.products.table.unit')">
                    <template #body="{ data }">
                        {{ data.unit?.name || '' }}
                    </template>
                </Column>

                <Column :header="t('directories.products.table.brand')">
                    <template #body="{ data }">
                        {{ data.brand?.name || '' }}
                    </template>
                </Column>

                <Column field="image" :header="t('directories.products.table.image')">
                    <template #body="{ data }">
                        <div class="image-cell">
                            <img :src="`${imageUrl}/images/products/${data.image}`" />
                        </div>
                    </template>
                </Column>

                <Column field="min_quantity" :header="t('directories.products.table.min_quantity')" sortable />

                <Column field="description" :header="t('directories.products.table.description')" />

                <Column field="status" :header="t('directories.products.table.status')" sortable>
                    <template #body="{ data }">
                        <Tag   :value="data.status ? t('global.status.active') : t('global.status.inactive')" :severity="data.status ? 'success' : 'danger'" />
                    </template>
                </Column>

                <Column style="width: 4.1rem" :exportable="false">
                    <template #body="{ data }">
                        <Button icon="pi pi-ellipsis-h" rounded text severity="secondary"
                            @click="openActionsMenu($event, data)" />
                    </template>
                </Column>
                <Menu ref="actionsMenu" :model="actionItems" :popup="true" />
            </DataTable>
            <Paginator class="isp-paginator" style="border: 1px solid var(--surface-border)" :rows="rows" :first="first"
                :totalRecords="total" :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown JumpToPageInput"
                @page="onPage">
                <template #start>
                    {{
                        total === 0
                            ? t('global.pagination.empty')
                            : t('global.pagination.report', {
                                first: first + 1,
                                last: Math.min(first + rows, total),
                                totalRecords: total
                            })
                    }}
                </template>

                <template #end>
                    <Button type="button" icon="pi pi-refresh" text @click="loadProducts" />
                    <Button type="button" icon="pi pi-download" text />
                </template>
            </Paginator>
            <ProductDialog />
            <DeleteProductDialog v-model="deleteProductDialog" :item-name="selectedProduct?.name"
                :loading="loadingDeleteProduct" @confirm="destroyProduct" />
            <DeleteProductsDialog v-model="deleteProductsDialog" :count="selectedProducts.length"
                :loading="loadingDeleteProducts" @confirm="destroyProducts" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import '@/assets/app-datatable.css';
import ProductMenu from '@/modules/directories/components/ProductMenu.vue';

import DeleteProductDialog from '@/modules/directories/components/DeleteProductDialog.vue';
import DeleteProductsDialog from '@/modules/directories/components/DeleteProductsDialog.vue';
import ProductDialog from '@/modules/directories/components/ProductDialog.vue';
import { useProductDialogStore } from '@/modules/directories/stores/productDialog.store';

import { getProducts, deleteProduct, deleteProducts } from '@/modules/directories/api/product.api';

import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const imageUrl = import.meta.env.VITE_API_URL;
const dialogStore = useProductDialogStore();
const toast = useToast();
const products = ref([]);
const selectedProduct = ref(null);
const selectedProducts = ref([]);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const loading = ref(false);
const loadingDeleteProduct = ref(false);
const loadingDeleteProducts = ref(false);
const search = ref('');
let searchTimer = null;
const rows = ref(10);
const first = ref(0);
const total = ref(0);
const sortField = ref('id');
const sortOrder = ref('asc');
const actionsMenu = ref(null);

const actionItems = computed(() => [
    {
        label: t('global.buttons.edit'),
        icon: 'pi pi-pencil',
        command: () => {
            editProduct(selectedProduct.value);
        }
    },
    {
        label: t('global.buttons.delete'),
        icon: 'pi pi-trash',
        command: () => {
            confirmDeleteProduct(selectedProduct.value);
        }
    }
]);

function openActionsMenu(event, product) {
    selectedProduct.value = product;
    actionsMenu.value.toggle(event);
}
async function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';

    first.value = 0;

    await loadProducts();
}

async function onPage(event) {
    first.value = event.first;
    rows.value = event.rows;

    await loadProducts();
}

function onSearch() {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(async () => {
        first.value = 0;

        await loadProducts();
    }, 400);
}

async function loadProducts() {
    loading.value = true;

    try {
        const page = first.value / rows.value + 1;

        const response = await getProducts(
            page,
            rows.value,
            sortField.value,
            sortOrder.value,
            search.value
        );

        products.value = response.data.data.data;
        total.value = response.data.data.total;

    } catch (error) {
        console.error(error);

        products.value = [];
        total.value = 0;

    } finally {
        loading.value = false;
    }
}

function addProduct() {
    dialogStore.openNew();
}

function editProduct(item) {
    dialogStore.openEdit(item);
}

function confirmDeleteProduct(item) {
    selectedProduct.value = item;
    deleteProductDialog.value = true;
}

function confirmDeleteProducts() {
    deleteProductsDialog.value = true;
}

async function destroyProduct() {
    if (
        loadingDeleteProduct.value ||
        !selectedProduct.value?.id
    ) {
        return;
    }

    loadingDeleteProduct.value = true;

    try {
        const response = await deleteProduct(
            selectedProduct.value.id
        );

        toast.add({
            severity: 'success',
            summary: t('global.messages.saved'),
            detail:
                response.data.message ||
                t('global.messages.deleted'),
            life: 3000
        });

        deleteProductDialog.value = false;
        selectedProduct.value = null;

        await loadProducts();

    } catch (error) {
        toast.add({
            severity: 'error',
            summary: t('global.messages.error'),
            detail:
                error.response?.data?.message ||
                t('directories.errors.deleteFailed'),
            life: 3000
        });

    } finally {
        loadingDeleteProduct.value = false;
    }
}

async function destroyProducts() {
    if (loadingDeleteProducts.value) {
        return;
    }

    const ids = selectedProducts.value.map(item => item.id);

    if (!ids.length) {
        return;
    }

    loadingDeleteProducts.value = true;

    try {
        const response = await deleteProducts(ids);

        toast.add({
            severity: 'success',
            summary: t('global.messages.deleted'),
            detail:
                response.data.message ||
                t('directories.success.deleteSelected'),
            life: 3000
        });

        deleteProductsDialog.value = false;
        selectedProducts.value = [];

        await loadProducts();

    } catch (error) {
        toast.add({
            severity: 'error',
            summary: t('global.messages.error'),
            detail:
                error.response?.data?.message ||
               t('directories.errors.deleteSelectedFailed'),
            life: 3000
        });

    } finally {
        loadingDeleteProducts.value = false;
    }
}

watch(
    () => dialogStore.changed,
    async (changed) => {
        if (!changed) {
            return;
        }

        await loadProducts();

        dialogStore.resetChanged();
    }
);

onMounted(() => {
    loadProducts();
});
</script>