<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        items: [{ label: 'Главная', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        items: [
            {
                label: 'Бухгалтерия',
                icon: 'pi pi-fw pi-wallet',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Продажи товаров',
                icon: 'pi pi-fw pi-shopping-cart',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Закуп товаров',
                icon: 'pi pi-fw pi-truck',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Склады',
                icon: 'pi pi-fw pi-warehouse',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Отчёты',
                icon: 'pi pi-fw pi-chart-bar',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Справочники',
                icon: 'pi pi-fw pi-book',
                items: [
                    {
                        label: 'Номенклатура',
                        icon: 'pi pi-fw pi-box',
                        to: '/directories/products'
                    },

                    {
                        label: 'Контрагенты',
                        icon: 'pi pi-fw pi-users',
                        to: '/directories/counterparties'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Настройки',
                icon: 'pi pi-fw pi-cog',
                items: [
                    {
                        label: 'Личный кабинет',
                        icon: 'pi pi-fw pi-user',
                        to: '/settings/profile'
                    },
                    {
                        label: 'Общие настройки',
                        icon: 'pi pi-fw pi-sliders-h',
                        to: '/settings/general'
                    },
                    {
                        label: 'Филиалы',
                        icon: 'pi pi-fw pi-building',
                        to: '/settings/branches'
                    },
                    {
                        label: 'Пользователи',
                        icon: 'pi pi-fw pi-users',
                        to: '/settings/users'
                    },
                    {
                        label: 'Валюты',
                        icon: 'pi pi-fw pi-dollar',
                        to: '/settings/currencies'
                    },
                    {
                        label: 'Счета',
                        icon: 'pi pi-fw pi-wallet',
                        to: '/settings/accounts'
                    },
                    {
                        label: 'Склады',
                        icon: 'pi pi-fw pi-warehouse',
                        to: '/settings/warehouses'
                    },
                    {
                        label: 'Начальные остатки',
                        icon: 'pi pi-fw pi-database',
                        to: '/settings/begin_balances'
                    }
                ]
            }
        ]
    },
    {
        items: [
            {
                label: 'Поддержка',
                icon: 'pi pi-fw pi-question-circle',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/'
                    },

                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/'
                    }
                ]
            }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
