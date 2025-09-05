import { defineStore } from 'pinia';
import { getTickets, getTicket, createTicket, getDashboardStats, classifyTicket } from '../api/ticket';

export const useTicketStore = defineStore('ticket', {
    state: () => ({
        ticketsList: [],
        loading: false,
        filter: {
            searchQuery: '',
            statusFilter: '',
            categoryFilter: '',
            currentPage: 1,
        },
        perPage: 15,
        total: 0,
        createModal: false,
        statuses: [],
        categories: [],
        dashboardStats: [],
    }),
    actions: {
        async getTickets() {
            this.loading = true;
            await getTickets( this.filter.currentPage, this.perPage, this.filter.searchQuery, this.filter.statusFilter, this.filter.categoryFilter)
            .then((res)=>{
                this.ticketsList = res?.tickets;
                this.categories = res?.categories;
                this.statuses = res?.statuses;
                this.total = res?.total;
                this.perPage = res?.perPage;
                this.filter.currentPage = res?.currentPage;
                this.loading = false;
            });
        },
        async getTicket(id) {
            const response = await getTicket(id);
            this.ticket = response.data;
        },
        async createTicket(ticket) {
            await createTicket(ticket).then((res)=>{
                this.ticket = res.data;
                this.createModal = false;
            }).catch((err)=>{
                console.error('Error creating ticket:', err);
            });
        },
        async getDashboardStats() {
            const response = await getDashboardStats();
            this.dashboardStats = response.data;
        },
        async classifyTicket(id) {
            const response = await classifyTicket(id);
            this.ticket = response.data;
        },
        openCreateModal() {
            this.createModal = true;
        },
        closeCreateModal() {
            this.createModal = false;
        }
    },
});
