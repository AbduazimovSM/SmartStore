import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';

// modules
import authRoutes from '@/modules/auth/router';
import dashboardRoutes from '@/modules/dashboard/router';
import directoryRoutes from '@/modules/directories/router';
import settingRoutes from '@/modules/settings/router';
import referenceRoutes from '@/modules/references/router';

const routes = [
    {
        path: '/',
        component: AppLayout,
        children: [
            ...dashboardRoutes,
            ...directoryRoutes,
            ...settingRoutes,
            ...referenceRoutes,
        ]
    },
    ...authRoutes,

];


const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
