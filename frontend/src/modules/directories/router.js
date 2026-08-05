export default [{
        path: 'directories/counterparties',
        name: 'directories.counterparties',
        component: () => import ('./pages/Counterparty.vue'),
        meta: { requiresAuth: true, module: 'directories' }
    },
    {
        path: 'directories/products',
        name: 'directories.products',
        component: () => import ('./pages/Product.vue'),
        meta: { requiresAuth: true, module: 'directories' }
    }
];