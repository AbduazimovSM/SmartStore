<template>
    <div class="card reference-page-card">
        <ProductMenu class="mb-8" />
        <Toolbar style="border-radius: 0">
            <template #start>
                <Button label="Добавить" icon="pi pi-plus" severity="success" class="mr-2" @click="addReference" />
                <Button icon="pi pi-trash" severity="danger" :disabled="!selectedReferences.length"
                    @click="confirmDeleteReferences">
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
                    <InputText v-model="search" placeholder="Search..." @input="onSearch" />
                </IconField>
            </template>
        </Toolbar>
        <div class="table-wrapper">
            <DataTable ref="dt" class="base-table text-center" v-model:selection="selectedReferences"
                :value="references" :loading="loading" showGridlines data-key="id" columnResizeMode="fit" scrollable scrollHeight="flex"
                :tableStyle="{ minWidth: '760px' }"
                 resizableColumns lazy :sortField="sortField"
                :sortOrder="sortOrder === 'asc' ? 1 : -1" @sort="onSort">
                <template #empty>
                    <p class="text-center">Данные не найдены</p>
                </template>

                <Column selection-mode="multiple" style="width: 3rem" :exportable="false" />

                <Column field="id" header="ID" sortable />

                <Column field="name" header="Наименование" sortable />

                <Column v-if="type === 'category'" field="parent_id" header="Род.категория" sortable>
                    <template #body="{ data }">
                        {{ getParentCategoryName(data.parent_id) }}
                    </template>
                </Column>

                <Column v-if="type === 'unit'" field="short_name" header="Краткое название" sortable>
                    <template #body="{ data }">
                        {{ data.short_name || '' }}
                    </template>
                </Column>

                <Column field="description" header="Примичание" sortable>
                    <template #body="{ data }">
                        {{ data.description || '' }}
                    </template>
                </Column>

                <Column field="status" header="Статус" sortable>
                    <template #body="{ data }">
                        <Tag :value="data.status ? 'Активен' : 'Неактивен'"
                            :severity="data.status ? 'success' : 'danger'" />
                    </template>
                </Column>

                <Column style="width: 4.1rem " :exportable="false">
                    <template #body="{ data }">
                        <Button icon="pi pi-ellipsis-h" rounded text severity="secondary" aria-label="Действия"
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
                    Показаны с {{ first + 1 }}
                    по {{ Math.min(first + rows, total) }}
                    из {{ total }} записей
                </template>

                <template #end>
                    <Button type="button" icon="pi pi-refresh" @click="loadReferences" text />
                    <Button type="button" icon="pi pi-download" text @click="exportExcel" />
                </template>
            </Paginator>
        </div>

        <Dialog v-model:visible="referenceDialog" maximizable :style="{ width: '450px' }" :header="dialogTitle" modal
            class="p-fluid">

            <div>
                <FloatLabel variant="on" class="mt-2">
                    <InputText id="name" v-model.trim="reference.name" autofocus :invalid="submitted && !reference.name"
                        fluid />
                    <label for="name">Наименование</label>
                </FloatLabel>
                <small v-if="submitted && !reference.name" class="p-error"> Наименование обязательно</small>
            </div>

            <div v-if="type === 'category'" class="field">
                <FloatLabel variant="on" class="mt-4">
                    <Select id="parent_id" v-model="reference.parent_id" :options="parentCategories" option-label="name"
                        option-value="id" show-clear fluid />
                    <label for="parent_id">Родительская категория</label>
                </FloatLabel>
            </div>

            <div v-if="type === 'unit'" class="field">
                <FloatLabel variant="on" class="mt-4">
                    <InputText id="short_name" v-model.trim="reference.short_name" fluid />
                    <label for="short_name">Краткое название</label>
                </FloatLabel>
            </div>

            <div class="field">
                <FloatLabel variant="on" class="mt-4">
                    <Textarea id="description" v-model="reference.description" rows="3" fluid />
                    <label for="description">Описание</label>
                </FloatLabel>
            </div>

            <div class="field">
                <FloatLabel variant="on" class="mt-4">
                    <Select id="status" v-model="reference.status" :options="statuses" option-label="label"
                        option-value="value" fluid />
                    <label for="status">Статус</label>
                </FloatLabel>
            </div>

            <!-- <template #footer> -->
            <div class="reference-dialog-actions">
                <Button label="Отмена" icon="pi pi-times" text :disabled="saving" @click="hideDialog" />
                <Button label="Сохранить" icon="pi pi-check" text :loading="saving" :disabled="saving"
                    @click="saveReference" />
            </div>
            <!-- </template> -->
        </Dialog>

        <Dialog v-model:visible="deleteReferenceDialog" :style="{ width: '450px' }" header="Подтверждение" modal>
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />

                <span>
                    Удалить
                    <b>{{ reference.name }}</b>?
                </span>
            </div>

            <template #footer>
                <Button label="Нет" icon="pi pi-times" text :disabled="deletingReference"
                    @click="deleteReferenceDialog = false" />

                <Button label="Да" icon="pi pi-check" text :loading="deletingReference"
                    :disabled="deletingReference" @click="destroyReference" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteReferencesDialog" :style="{ width: '450px' }" header="Подтверждение" modal>
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl text-yellow-500" />
                <span>
                    Удалить <b>{{ selectedReferences.length }}</b> выбранные записи?
                </span>
            </div>

            <template #footer>
                <Button label="Нет" icon="pi pi-times" text :disabled="deletingReferences"
                    @click="deleteReferencesDialog = false" />
                <Button label="Да" icon="pi pi-check" text :loading="deletingReferences"
                    :disabled="deletingReferences" @click="destroyReferences" />
            </template>
        </Dialog>
    </div>

</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import '@/assets/app-datatable.css';
import ProductMenu from '@/modules/directories/components/ProductMenu.vue';
import * as XLSX from 'xlsx';
import {
    getReferences,
    createReference,
    updateReference,
    deleteReference,
    deleteReferences
} from '@/modules/references/api/reference.api';

const route = useRoute();
const toast = useToast();

const actionsMenu = ref(null);
const selectedReference = ref(null);

const actionItems = [
    {
        label: 'Изменить',
        icon: 'pi pi-pencil',
        command: () => {
            editReference(selectedReference.value);
        }
    },
    {
        label: 'Удалить',
        icon: 'pi pi-trash',
        command: () => {
            confirmDeleteReference(selectedReference.value);
        }
    }
];

function openActionsMenu(event, reference) {
    selectedReference.value = reference;
    actionsMenu.value.toggle(event);
}
const dt = ref();
const references = ref([]);
const selectedReferences = ref([]);
const parentCategories = ref([]);

const loading = ref(false);
const saving = ref(false);
const deletingReference = ref(false);
const deletingReferences = ref(false);
const submitted = ref(false);

const rows = ref(10);
const first = ref(0);
const total = ref(0);
const sortField = ref('id');
const sortOrder = ref('asc');
const search = ref('');
let searchTimer = null;

function onSearch() {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(async () => {
        first.value = 0;
        await loadReferences();
    }, 400);
}
function exportExcel() {
    const data = references.value.map(item => ({
        ID: item.id,
        'Наименование': item.name,
        'Краткое название': item.short_name || '',
        'Примечание': item.description || '',
        'Статус': item.status ? 'Активен' : 'Неактивен'
    }));

    const worksheet = XLSX.utils.json_to_sheet(data);

    const workbook = XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(
        workbook,
        worksheet,
        'Справочник'
    );

    XLSX.writeFile(
        workbook,
        `${type.value}.xlsx`
    );
}

const referenceDialog = ref(false);
const deleteReferenceDialog = ref(false);
const deleteReferencesDialog = ref(false);

const reference = ref({});

const type = computed(() => route.query.type || 'category');

const pageTitle = computed(() => {
    const titles = {
        category: 'Категории',
        unit: 'Единицы измерения',
        brand: 'Бренды'
    };

    return titles[type.value] || 'Справочники';
});

const dialogTitle = computed(() => {
    return reference.value.id
        ? `Редактировать: ${pageTitle.value}`
        : `Добавить: ${pageTitle.value}`;
});

const statuses = [
    { label: 'Активен', value: true },
    { label: 'Неактивен', value: false }
];

async function onSort(event) {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';

    first.value = 0;

    await loadReferences();
}

async function onPage(event) {
    first.value = event.first;
    rows.value = event.rows;

    await loadReferences();
}

function emptyReference() {
    return {
        type: type.value,
        name: '',
        short_name: null,
        parent_id: null,
        description: '',
        status: true
    };
}

async function loadReferences() {
    loading.value = true;

    try {
        const page = first.value / rows.value + 1;

        const response = await getReferences(
            type.value,
            page,
            rows.value,
            sortField.value,
            sortOrder.value,
            search.value
        );

        references.value = response.data.data.data;
        total.value = response.data.data.total;

    } catch (error) {
        console.error('Ответ ошибки:', error.response?.data);
        references.value = [];
        total.value = 0;
    } finally {
        loading.value = false;
    }
}

async function loadParentCategories() {
    if (type.value !== 'category') {
        parentCategories.value = [];
        return;
    }

    const response = await getReferences(
        'category',
        1,
        100,
        'id',
        'asc',
        ''
    );
    parentCategories.value = response.data.data.data;
}

function getParentCategoryName(parentId) {
    if (!parentId) {
        return '';
    }

    const parent = parentCategories.value.find(
        category => category.id === parentId
    );

    return parent?.name || '';
}

function addReference() {
    reference.value = emptyReference();
    submitted.value = false;
    referenceDialog.value = true;
}
function hideDialog() {
    referenceDialog.value = false;
    submitted.value = false;
    reference.value = emptyReference();
}

function editReference(item) {
    reference.value = { ...item };
    submitted.value = false;
    referenceDialog.value = true;
}

async function saveReference() {
    if (saving.value) {
        return;
    }

    submitted.value = true;

    if (!reference.value.name?.trim()) {
        return;
    }

    saving.value = true;

    try {
        let response;
        if (reference.value.id) {
            response = await updateReference(reference.value.id, reference.value);
        }
        else {
            response = await createReference(reference.value);
        }
        toast.add({ severity: 'success', summary: 'Успешно', detail: response.data.message, life: 3000 });

        referenceDialog.value = false;
        reference.value = emptyReference();
        submitted.value = false;
        await loadReferences();
    }
    catch (error) {
        toast.add({ severity: 'error', summary: 'Ошибка', detail: error.response?.data?.message || 'Не удалось добавить запись', life: 3000 });
    } finally {
        saving.value = false;
    }
}

function confirmDeleteReference(item) {
    reference.value = item;
    deleteReferenceDialog.value = true;
}

async function destroyReference() {
    if (deletingReference.value || !reference.value.id) {
        return;
    }

    deletingReference.value = true;

    try {
        const response = await deleteReference(reference.value.id);

        toast.add({
            severity: 'success',
            summary: 'Успешно',
            detail: response.data.message || 'Запись удалена',
            life: 3000
        });

        deleteReferenceDialog.value = false;
        reference.value = emptyReference();
        await loadReferences();
    } catch (error) {
        console.error('Ошибка удаления:', error);
        console.error('Статус:', error.response?.status);
        console.error('Ответ backend:', error.response?.data);

        toast.add({
            severity: 'error',
            summary: 'Ошибка',
            detail:
                error.response?.data?.message ||
                'Не удалось удалить запись',
            life: 3000
        });
    } finally {
        deletingReference.value = false;
    }
}

function confirmDeleteReferences() {
    deleteReferencesDialog.value = true;
}

async function destroyReferences() {
    if (deletingReferences.value) {
        return;
    }

    const ids = selectedReferences.value.map(item => item.id);

    if (!ids.length) {
        return;
    }

    deletingReferences.value = true;

    try {
        const response = await deleteReferences(ids);

        toast.add({
            severity: 'success',
            summary: 'Успешно',
            detail:
                response.data.message ||
                'Выбранные записи удалены',
            life: 3000
        });

        deleteReferencesDialog.value = false;
        selectedReferences.value = [];

        await loadReferences();
    } catch (error) {
        console.error('Ошибка массового удаления:', error);
        console.error('Статус:', error.response?.status);
        console.error('Ответ backend:', error.response?.data);

        toast.add({
            severity: 'error',
            summary: 'Ошибка',
            detail:
                error.response?.data?.message ||
                'Не удалось удалить выбранные записи',
            life: 3000
        });
    } finally {
        deletingReferences.value = false;
    }
}

watch(
    () => route.query.type,
    async () => {
        selectedReferences.value = [];
        reference.value = emptyReference();

        first.value = 0;
        search.value = '';

        await loadReferences();
        await loadParentCategories();
    },
    { immediate: true }
);

</script>
