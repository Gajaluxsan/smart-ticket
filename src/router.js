import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from './view/Dashboard.vue'
import AppLayout from './layouts/AppLayout.vue'
import Tickets from './view/Tickets.vue'
import TicketDetail from './view/TicketDetail.vue'

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: AppLayout, 
            redirect: '/dashboard',
            children: [
                { path: '/dashboard', component: Dashboard },
                { path: '/tickets', component: Tickets },
                { path: '/tickets/:id', component: TicketDetail },
            ]
        }
    ]
});