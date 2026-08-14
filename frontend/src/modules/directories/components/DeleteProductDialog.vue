<template>
    <Dialog
        v-model:visible="visible"
        header="Подтверждение"
        :style="{ width: '450px' }"
        modal
    >
        <div class="flex align-items-center gap-3">
            <i
                class="pi pi-exclamation-triangle"
                style="font-size: 2rem"
            />

            <span>
                Вы действительно хотите удалить
                <b>{{ itemName }}</b>?
            </span>
        </div>

        <template #footer>
            <Button
                label="Нет"
                icon="pi pi-times"
                text
                :disabled="loading"
                @click="visible = false"
            />

            <Button
                label="Да"
                icon="pi pi-check"
                severity="danger"
                :loading="loading"
                :disabled="loading"
                @click="$emit('confirm')"
            />
        </template>
    </Dialog>
</template>

<script setup>
const visible = defineModel({
    type: Boolean,
    default: false
});

defineProps({
    itemName: {
        type: String,
        default: ''
    },

    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['confirm']);
</script>