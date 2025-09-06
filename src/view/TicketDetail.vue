<template>
    <div class="page">
        <!-- Header -->
        <div class="page__header">
            <div class="page__header-top">
                <button 
                    class="btn btn--secondary"
                    @click="$router.go(-1)"
                >
                    ← Back to Tickets
                </button>
                <div class="ticket-detail__actions">
                    <button 
                        class="btn btn--primary"
                        @click="toggleEditMode"
                        :disabled="loading"
                    >
                        {{ isEditing ? 'Cancel' : 'Edit Ticket' }}
                    </button>
                    <button 
                        class="btn btn--primary"
                        @click="runClassification"
                        :disabled="classifying || loading"
                    >
                        <span v-if="classifying" class="spinner"></span>
                        {{ classifying ? 'Classifying...' : 'Run Classification' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading">
            <div class="loading__spinner"></div>
            <p>Loading ticket details...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="card">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                <h3 style="color: var(--text-primary); margin: 0 0 0.5rem 0;">Failed to load ticket</h3>
                <p style="color: var(--text-secondary); margin: 0 0 1.5rem 0;">{{ error }}</p>
                <button class="btn btn--primary" @click="loadTicket">Retry</button>
            </div>
        </div>

        <!-- Ticket Content -->
        <div v-else-if="ticket" class="ticket-detail__content">
            <div class="ticket-detail__grid">
                <!-- Main Ticket Info -->
                <div class="ticket-detail__main">
                    <div class="card">
                        <div class="card__header">
                            <h1 class="page__title" style="font-size: 1.5rem; margin-bottom: 0.75rem;">{{ ticket.subject }}</h1>
                            <div class="ticket-card__meta">
                                <span class="ticket-id">#{{ ticket.id }}</span>
                                <span class="ticket-date">{{ formatDate(ticket.created_at) }}</span>
                            </div>
                        </div>
                        
                        <div class="card__content">
                            <h3 style="color: var(--text-primary); font-size: 1rem; font-weight: 600; margin: 0 0 1rem 0;">Description</h3>
                            <div class="ticket-description">
                                {{ ticket.body }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Details & Edit Form -->
                <div class="ticket-detail__sidebar">
                    <!-- View Mode -->
                    <div v-if="!isEditing" class="ticket-details">
                        <div class="card">
                            <div class="card__header">
                                <h3 class="card__title">Ticket Details</h3>
                            </div>
                            
                            <div class="form__group">
                                <label class="form__label">Status</label>
                                <div>
                                    <span class="badge badge--status" :class="`badge--${ticket.status}`">
                                        {{ formatStatus(ticket.status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="form__group">
                                <label class="form__label">Category</label>
    <div>
                                    <span class="badge badge--category">
                                        {{ ticket.category || 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="ticket.note" class="form__group">
                                <label class="form__label">Internal Note</label>
                                <div class="note-content">
                                    {{ ticket.note }}
                                </div>
                            </div>

                            <!-- AI Classification Results -->
                            <div v-if="ticket.explanation || ticket.confidence" class="ai-classification">
                                <h4 class="ai-classification__title">🤖 AI Classification</h4>
                                
                                <div v-if="ticket.explanation" class="form__group">
                                    <label class="form__label">Explanation</label>
                                    <div class="explanation-content">
                                        {{ ticket.explanation }}
                                    </div>
                                </div>

                                <div v-if="ticket.confidence" class="form__group">
                                    <label class="form__label">Confidence</label>
                                    <div class="confidence-display">
                                        <div class="progress">
                                            <div class="progress__bar">
                                                <div 
                                                    class="progress__fill"
                                                    :style="{ width: (ticket.confidence * 100) + '%' }"
                                                ></div>
                                            </div>
                                            <span class="progress__text">
                                                {{ Math.round(ticket.confidence * 100) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div v-else class="card">
                        <div class="card__header">
                            <h3 class="card__title">Edit Ticket</h3>
                        </div>
                        <form @submit.prevent="saveTicket">
                            <div class="form__group">
                                <label class="form__label">Status</label>
                                <select 
                                    v-model="editForm.status" 
                                    class="form__select"
                                    required
                                >
                                    <option v-for="status in statuses" :value="status.value">{{ status.label }}</option>
                                </select>
                            </div>

                            <div class="form__group">
                                <label class="form__label">Category</label>
                                <input 
                                    type="text" 
                                    v-model="editForm.category" 
                                    class="form__input"
                                    placeholder="Enter category"
                                    list="categories"
                                >
                                <datalist id="categories">
                                    <option v-for="category in categories" :value="category.label">{{ category.label }}</option>
                                </datalist>
                            </div>

                            <div class="form__group">
                                <label class="form__label">Internal Note</label>
                                <textarea 
                                    v-model="editForm.note" 
                                    class="form__textarea"
                                    rows="4"
                                    placeholder="Add internal notes..."
                                ></textarea>
                            </div>

                            <div class="modal__actions">
                                <button 
                                    type="button" 
                                    class="btn btn--secondary"
                                    @click="cancelEdit"
                                    :disabled="saving"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="btn btn--primary"
                                    :disabled="saving"
                                >
                                    <span v-if="saving" class="spinner"></span>
                                    {{ saving ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useTicketStore } from '../stores/ticket';
import { mapActions, mapState } from 'pinia';
import { updateTicket, classifyTicket } from '../api/ticket';

export default {
    name: 'TicketDetail',
    data() {
        return {
            ticket: null,
            loading: false,
            error: null,
            isEditing: false,
            saving: false,
            classifying: false,
            editForm: {
                status: '',
                category: '',
                note: ''
            }
        }
    },
    computed: {
        ...mapState(useTicketStore, {
            statuses: 'statuses',
            categories: 'categories'
        })
    },
    async mounted() {
        await this.loadTicket();
    },
    methods: {
        ...mapActions(useTicketStore, {
            getTicket: 'getTicket'
        }),
        async loadTicket() {
            this.loading = true;
            this.error = null;
            try {
                const ticketId = this.$route.params?.id;
                if (!ticketId) {
                    throw new Error('No ticket ID provided');
                }
                const ticket = await this.getTicket(ticketId);
                this.ticket = ticket;
                this.resetEditForm();
            } catch (error) {
                this.error = 'Failed to load ticket details. Please try again.';
                console.error('Error loading ticket:', error);
            } finally {
                this.loading = false;
            }
        },
        toggleEditMode() {
            if (this.isEditing) {
                this.cancelEdit();
            } else {
                this.startEdit();
            }
        },
        startEdit() {
            this.isEditing = true;
            this.resetEditForm();
        },
        cancelEdit() {
            this.isEditing = false;
            this.resetEditForm();
        },
        resetEditForm() {
            if (this.ticket) {
                this.editForm = {
                    status: this.ticket.status || 'pending',
                    category: this.ticket.category || '',
                    note: this.ticket.note || ''
                };
            }
        },
        async saveTicket() {
            this.saving = true;
                const ticketId = this.$route.params?.id;
                await updateTicket(ticketId, this.editForm)
                    .then((res) =>{
                        this.ticket = res.data;
                        this.isEditing = false;
                        this.saving = false;
                    })
                    .catch((err) => {
                        console.error('Error updating ticket:', err);
                    });
        },
        async runClassification() {
            this.classifying = true;
            try {
                const ticketId = this.$route.params?.id;
                await classifyTicket(ticketId).then((res) => {
                    console.log(res);
                    this.ticket = res.data;
                    this.classifying = false;
                }).catch((err) => {
                    console.error('Error classifying ticket:', err);
                });
            } catch (error) {
                console.error('Error classifying ticket:', error);
            } finally {
                this.classifying = false;
            }
        },
        formatStatus(status) {
            return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>