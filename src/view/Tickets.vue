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
            @classifyTicket="classifyTicket" 
            :loading="loading" 
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

export default {
    name: 'Tickets',
    components: {
        TicketTable,
        CreateTicketModel
    },
    data() {
        return {
            
            // Pagination
            
            // Filters & Search
            searchQuery: '',
            statusFilter: '',
            categoryFilter: '',
            
            // UI State
            classifyingTickets: new Set(),
            creatingTicket: false,
            
            // New ticket form
            newTicket: {
                subject: '',
                body: ''
            }
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
        async classifyTicket(ticketId) {
            this.classifyingTickets.add(ticketId)
            try {
                await axios.post(`/api/tickets/${ticketId}/classify`)
                this.getTicketsAll()
                alert('Ticket classification started!')
            } catch (error) {
                console.error('Error classifying ticket:', error)
                alert('Failed to classify ticket')
            } finally {
                this.classifyingTickets.delete(ticketId)
            }
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