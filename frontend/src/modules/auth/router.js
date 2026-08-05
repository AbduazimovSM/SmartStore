export default [
        {
            path: '/auth/login',
            name: 'login',
            component: () => import('@/modules/auth/pages/Login.vue')
        },
        {
            path: '/auth/register',
            name: 'register',
            component: () => import('@/modules/auth/pages/Register.vue')
        }
]