<template>
    <div v-if="createModal" class="modal" @click="closeCreateModal">
        <div class="modal__content" @click.stop>
            <div class="modal__header">
                <h2 class="modal__title">Create New Ticket</h2>
                <button class="modal__close" @click="closeCreateModal">
                    ×
                </button>
            </div>

            <form @submit.prevent="createTicket" class="modal__body">
                <div class="form__group">
                    <label class="form__label">Subject</label>
                    <input
                        type="text"
                        v-model="ticket.subject"
                        class="form__input"
                        required
                    />
                </div>

                <div class="form__group">
                    <label class="form__label">Description</label>
                    <textarea
                        v-model="ticket.body"
                        class="form__textarea"
                        rows="4"
                        required
                    ></textarea>
                </div>

                <div class="modal__actions">
                    <button
                        type="button"
                        class="btn btn--secondary"
                        @click="closeCreateModal"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="btn btn--primary"
                        :disabled="creatingTicket"
                    >
                        <span v-if="creatingTicket" class="spinner"></span>
                        <span v-else>Create Ticket</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import { useTicketStore } from '../stores/ticket';
import { mapState, mapActions } from 'pinia';

export default {
    name: 'CreateTicketModel',
    data() {
        return {
            ticket: {
                subject: '',
                body: '',
                status: 'pending',
                category: '',
                note: '',
            },
        }
    },
    computed: {
        ...mapState(useTicketStore, {
            createModal: 'createModal',
        }),
    },
    methods: {
        ...mapActions(useTicketStore, {
            createTicket: 'createTicket',
            closeCreateModal: 'closeCreateModal',
        }),
        async createTicket() {
            await this.createTicket(this.ticket);
        },
    },
}
</script>
