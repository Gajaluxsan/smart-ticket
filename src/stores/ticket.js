import { defineStore } from 'pinia';
import { getTickets, getTicket, createTicket, getDashboardStats } from '../api/ticket';

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
        errors: [],
        categories: [],
        dashboardStats: {
            total: 0,
            statuses: {
                labels: [],
                datasets: [],
                total: 0,
            },
            categories: {
                labels: [],
                datasets: [],
                total: 0,
            },
            statusesCount: {
                closed: 0,
                open: 0,
                resolved: 0,
                in_progress: 0,
            },
        },
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
            return await getTicket(id).then( async (res)=>{
                return res.data;
            });
        },
        async createTicket(ticket) {
            await createTicket(ticket).then((res)=>{
                this.ticket = res.data;
                this.createModal = false;
                this.getTickets();
            }).catch((err)=>{
                const { message, errors } = err.response.data;
                this.errors = errors;
                console.error('Error creating ticket:', err);
            });
        },
        async getDashboardStats() {
            await getDashboardStats().then((res)=>{
                const { statuses, categories, total, statusesCount } = res;
                this.dashboardStats.statuses.labels = statuses.labels;
                this.dashboardStats.statuses.datasets = statuses.datasets;
                this.dashboardStats.categories = categories;
                this.dashboardStats.total = total;
                this.dashboardStats.statusesCount = statusesCount;
            });
        },
        resetErrors() {
            this.errors = [];
        },
        openCreateModal() {
            this.createModal = true;
        },
        closeCreateModal() {
            this.createModal = false;
        }
    },
});
