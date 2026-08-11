
<template>
    <div class="card">
        <Menubar :model="items" />
    </div>
</template>

<script setup>
import { ref } from "vue";

const items = ref([
    {
        label: 'Номенклатура',
        icon: 'pi pi-box',
        route: '/directories/products'
    },
    {
        label: 'Категория',
        icon: 'pi pi-list',
        route: '/directories/products'
    },
    {
        label: 'Единица измерения',
        icon: 'pi pi-tag',
        route: '/directories/products'
    },
    {
        label: 'Бренды',
        icon: 'pi pi-tags',
        route: '/directories/products'
    }
]);
</script>
