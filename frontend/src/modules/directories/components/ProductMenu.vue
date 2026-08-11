<template>
    <TabMenu
        v-model:activeIndex="activeIndex"
        :model="items"
        @tab-change="changeTab"
    />
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const items = [
    {
        label: 'Номенклатура',
        icon: 'pi pi-box',
        route: {
            path: '/directories/products'
        }
    },
    {
        label: 'Категория',
        icon: 'pi pi-tags',
        route: {
            path: '/references',
            query: { type: 'category' }
        }
    },
    {
        label: 'Единица измерения',
        icon: 'pi pi-list',
        route: {
            path: '/references',
            query: { type: 'unit' }
        }
    },
    {
        label: 'Бренды',
        icon: 'pi pi-building',
        route: {
            path: '/references',
            query: { type: 'brand' }
        }
    }
];

const activeIndex = computed({
    get() {
        if (route.path === '/directories/products') {
            return 0;
        }

        const indexes = {
            category: 1,
            unit: 2,
            brand: 3
        };

        return indexes[route.query.type] ?? 0;
    },

    set() {
        // Значение меняется через changeTab()
    }
});

function changeTab(event) {
    router.push(items[event.index].route);
}
</script> 