<template>
    <Dialog
        v-model:visible="visible"
        :style="{ width: '450px' }"
        header="Подтверждение"
        modal
    >
        <div class="confirmation-content">
            <i
                class="pi pi-exclamation-triangle mr-3"
                style="font-size: 2rem"
            />

            <span>
                Удалить <b>{{ itemName }}</b>?
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
    itemName: {
        type: String,
        default: ''
    },

    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['confirm']);
</script>