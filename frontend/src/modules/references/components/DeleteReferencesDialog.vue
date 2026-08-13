<template>
    <Dialog
        v-model:visible="visible"
        :style="{ width: '450px' }"
        header="Подтверждение"
        modal
    >
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl text-yellow-500" />

            <span>
                Удалить <b>{{ count }}</b> выбранные записи?
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
                text
                :loading="loading"
                :disabled="loading"
                @click="emit('confirm')"
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
    count: {
        type: Number,
        default: 0
    },

    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['confirm']);
</script>