import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './view/Dashboard.vue'
import AppLayout from './layouts/AppLayout.vue'

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: AppLayout, 
            children: [
                { path: '/', component: Dashboard },
            ]
        }
    ]
});