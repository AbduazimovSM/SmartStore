<template>
    <div>
        <div class="card">
            <ProductMenu class="mb-8" />
            <Toolbar>
                <template #start>
                    <Button label="Добавить" icon="pi pi-plus" severity="success" class="mr-2" @click="addReference" />
                    <Button label="Удалить" icon="pi pi-trash" severity="danger" :disabled="!selectedReferences.length"
                        @click="confirmDeleteReferences" />
                </template>

            <template #end>
                <IconField iconPosition="left">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText placeholder="Search..." />
                </IconField>
            </template>
        </Toolbar>
        <div class="table-wrapper">
            <DataTable class="base-table" ref="dt" v-model:selection="selectedReferences" :value="references"
                :loading="loading" showGridlines data-key="id" resizableColumns columnResizeMode="fit">

                <template #empty>
                    <p class="text-center">Данные не найдены</p>
                </template>

                <Column selection-mode="multiple" style="width: 3rem" :exportable="false" />

                <Column field="id" header="ID" sortable />

                <Column field="name" header="Наименование" sortable />

                <Column v-if="type === 'category'" field="parent_id" header="Родительская категория" sortable>
                    <template #body="{ data }">
                        {{ data.parent_id ?? '' }}
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

                <Column header="Действия" :exportable="false">
                    <template #body="{ data }">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editReference(data)" />

                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteReference(data)" />
                    </template>
                </Column>
            </DataTable>
            <Paginator class="isp-paginator" style="border: 1px solid var(--surface-border)" :rows="rows"
                :first="first" :totalRecords="total" :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown JumpToPageInput"
                @page="onPage">


                <template #end>
                    <Button type="button" icon="pi pi-refresh" text />
                    <Button icon="pi pi-plus" class="mr-2" severity="secondary" text />
                    <Button icon="pi pi-print" class="mr-2" severity="secondary" text />
                    <Button icon="pi pi-upload" severity="secondary" text />
                    <Button type="button" icon="pi pi-download" text />
                </template>
            </Paginator>
        </div>
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
        <div class="flex justify-content-center">
            <Button label="Отмена" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Сохранить" icon="pi pi-check" text @click="saveReference" />
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
            <Button label="Нет" icon="pi pi-times" text @click="deleteReferenceDialog = false" />

            <Button label="Да" icon="pi pi-check" text @click="destroyReference" />
        </template>
    </Dialog>

    <Dialog v-model:visible="deleteReferencesDialog" :style="{ width: '450px' }" header="Подтверждение" modal>
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl text-yellow-500" />
            <span>
                Удалить выбранные записи:
                <b>{{ selectedReferences.length }}</b>?
            </span>
        </div>

        <template #footer>
            <Button label="Нет" icon="pi pi-times" text @click="deleteReferencesDialog = false" />
            <Button label="Да" icon="pi pi-check" text @click="destroyReferences" />
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
import {
    getReferences,
    createReference,
    updateReference,
    deleteReference,
    deleteReferences
} from '@/modules/references/api/reference.api';

const route = useRoute();
const toast = useToast();

const dt = ref();
const references = ref([]);
const selectedReferences = ref([]);
const parentCategories = ref([]);

const loading = ref(false);
const submitted = ref(false);
const rows = ref(10);
const first = ref(0);

const total = computed(() => references.value.length);


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

function onPage(event) {
    first.value = event.first;
    rows.value = event.rows;
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
        const response = await getReferences(type.value);
        references.value = response.data.data;

    } catch (error) {
        console.error('Ответ ошибки:', error.response?.data);
        references.value = [];
    } finally {
        loading.value = false;
    }
}

async function loadParentCategories() {
    if (type.value !== 'category') {
        parentCategories.value = [];
        return;
    }

    const response = await getReferences('category');
    parentCategories.value = response.data.data;
}

function addReference() {
    reference.value = emptyReference();
    submitted.value = false;
    referenceDialog.value = true;
}

function editReference(item) {
    reference.value = { ...item };
    submitted.value = false;
    referenceDialog.value = true;
}

async function saveReference() {
    submitted.value = true;

    if (!reference.value.name?.trim()) {
        return;
    }
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
    }


}

function confirmDeleteReference(item) {
    reference.value = item;
    deleteReferenceDialog.value = true;
}

async function destroyReference() {
    if (!reference.value.id) {
        return;
    }

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
    }
}

function confirmDeleteReferences() {
    deleteReferencesDialog.value = true;
}

async function destroyReferences() {
    const ids = selectedReferences.value.map(item => item.id);

    if (!ids.length) {
        return;
    }

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
    }
}

watch(
    () => route.query.type,
    async () => {
        selectedReferences.value = [];
        reference.value = emptyReference();

        await loadReferences();
        await loadParentCategories();
    },
    { immediate: true }
);

</script>