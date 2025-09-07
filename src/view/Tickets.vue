<template>
    <div class="page">
        <!-- Header Section -->
        <div class="page__header">
            <div class="page__header-top">
                <h1 class="page__title">Tickets</h1>
                <button 
                    class="btn btn--primary"
                    @click="openCreateModal"
                >
                    New Ticket
                </button>
            </div>
            
            <!-- Search and Filters -->
            <div class="page__controls">
                <div class="search">
                    <input 
                        type="text" 
                        v-model="filter.searchQuery"
                        @input="handleSearch"
                        placeholder="Search tickets..."
                        class="search__input"
                    >
                </div>
                
                <div class="filters">
                    <select 
                        v-model="filter.statusFilter" 
                        @change="handleFilter"
                        class="filter__select"
                    >
                        <option value="">All Status</option>
                        <option 
                            v-for="status in statuses" 
                            :key="status.value" 
                            :value="status.value"
                        >
                            {{ status.label }}
                        </option>
                    </select>
                    
                    <select 
                        v-model="filter.categoryFilter" 
                        @change="handleFilter"
                        class="filter__select"
                    >
                        <option value="">All Categories</option>
                        <option 
                            v-for="category in categories" 
                            :key="category.value" 
                            :value="category.value"
                        >
                            {{ category.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <TicketTable 
            :tickets="tickets" 
            @classifyTicket="onClassifyTicket" 
            :loading="loading" 
            :classifyingId="classifyingId"
            :classifyingLoading="classifyingLoading"
            :total="total" 
            :perPage="perPage" 
            :currentPage="currentPage" 
            @goToPage="goToPage"
        />

        <CreateTicketModel/>
    </div>
</template>

<script>
import { useTicketStore } from '../stores/ticket'
import { mapActions, mapState } from 'pinia';
import TicketTable from '../components/TicketTable.vue';
import CreateTicketModel from '../components/CreateTicketModel.vue';
import { classifyTicket } from '../api/ticket';

export default {
    name: 'Tickets',
    components: {
        TicketTable,
        CreateTicketModel
    },
    data() {
        return {
            classifyingLoading: false,
            classifyingId: null,
        }
    },
    computed: {
        ...mapState(useTicketStore, {
            tickets: 'ticketsList',
            loading: 'loading',
            total: 'total',
            filter: 'filter',
            perPage: 'perPage',
            statuses: 'statuses',
            categories: 'categories',
            dashboardStats: 'dashboardStats',
            createModal: 'createModal',
        }),
        currentPage() {
            return this.filter.currentPage;
        },
    },
    mounted() {
        this.getTicketsAll()
    },
    methods: {   
        ...mapActions(useTicketStore, {
            getTicketsAll: 'getTickets',
            openCreateModal: 'openCreateModal',
            closeCreateModal: 'closeCreateModal',
            createTicket: 'createTicket',
        }),       
        async onClassifyTicket(ticketId) {
            this.classifyingLoading = true;
            this.classifyingId = ticketId;
            await classifyTicket(ticketId).then((res) => {
                const {message, data} = res;
                if (data.status == 'success') {
                    this.tickets.find(ticket => ticket.id == ticketId).category = data.category;
                    this.tickets.find(ticket => ticket.id == ticketId).confidence = data.confidence;
                    this.tickets.find(ticket => ticket.id == ticketId).explanation = data.explanation;
                }
            }).catch((err) => {
                console.error('Error classifying ticket:', err);
            }).finally(() => {
                this.classifyingLoading = false;
                this.classifyingId = null;
            });
        },
        
        handleSearch() {
            this.filter.currentPage = 1
            this.debounceSearch()
        },
        
        handleFilter() {
            this.filter.currentPage = 1
            this.getTicketsAll()
        },
        
        goToPage(page) {
            if (page >= 1 && page <= this.total) {
                this.filter.currentPage = page
                this.getTicketsAll()
            }
        },
        openNewTicketModal() {
            this.openCreateModal()
        },        
        debounceSearch() {
            clearTimeout(this.searchTimeout)
            this.searchTimeout = setTimeout(() => {
                this.getTicketsAll()
            }, 300)
        }
    }
}
</script>