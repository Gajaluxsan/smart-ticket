<template>
    <div v-if="createModal" class="modal" @click="closeCreateModal">
        <div class="modal__content" @click.stop>
            <div class="modal__header">
                <h2 class="modal__title">Create New Ticket</h2>
                <button class="modal__close" @click="closeCreateModal">
                    ×
                </button>
            </div>

            <form @submit.prevent="onSubmitCreateTicket" class="modal__body">
                <div class="form__group">
                    <label class="form__label">Subject</label>
                    <input
                        type="text"
                        v-model="ticket.subject"
                        class="form__input"
                        :class="{ 'form__input--error': errors.subject }"
                    />
                    <div class="form__error" v-if="v$.ticket.subject.$errors[0]">
                        {{ v$.ticket.subject.$errors[0].$message }}
                    </div>
                    <div class="form__error" v-else-if="errors.subject">
                        {{ errors.subject[0] }}
                    </div>
                </div>

                <div class="form__group">
                    <label class="form__label">Description</label>
                    <textarea
                        v-model="ticket.body"
                        class="form__textarea"
                        :class="{ 'form__textarea--error': errors.body }"
                        rows="4"
                    ></textarea>
                    <div class="form__error" v-if="v$.ticket.body.$errors[0]">
                        {{ v$.ticket.body.$errors[0].$message }}
                    </div>
                    <div class="form__error" v-else-if="errors.body">
                        {{ errors.body[0] }}
                    </div>
                </div>

                <div class="form__group">
                    <label class="form__label">Note</label>
                    <textarea
                        v-model="ticket.note"
                        class="form__textarea"
                        :class="{ 'form__textarea--error': errors.note }"
                        rows="4"
                    ></textarea>
                    <div class="form__error" v-if="errors.note">
                        {{ errors.note[0] }}
                    </div>
                </div>

                <div class="form__group">
                    <label class="form__label">Status</label>
                    <select
                        v-model="ticket.status"
                        class="form__select"
                        :class="{ 'form__select--error': errors.status }"
                    >
                        <option v-for="status in statuses" :value="status.value">{{ status.label }}</option>
                    </select>
                    <div class="form__error" v-if="errors.status">
                        {{ errors.status[0] }}
                    </div>
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
                    >
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import { useTicketStore } from '../stores/ticket';
import { mapState, mapActions } from 'pinia';
import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

export default {
    name: 'CreateTicketModel',
    data() {
        return {
            v$: useVuelidate(),
            ticket: {
                subject: '',
                body: '',
                status: 'pending',
                category: '',
                note: '',
            },
        }
    },
    validations() {
        return {
            ticket: {
                subject: { required },
                body: { required },
            }
        }
    },
    created() {
        this.resetForm();
    },
    computed: {
        ...mapState(useTicketStore, {
            createModal: 'createModal',
            statuses: 'statuses',
            errors: 'errors',
        }),
    },
    methods: {
        ...mapActions(useTicketStore, {
            createTicket: 'createTicket',
            closeCreateModal: 'closeCreateModal',
            resetErrors: 'resetErrors',
        }),
        async onSubmitCreateTicket() {
            const isValid = await this.v$.$validate();
            if (!isValid) {
                return;
            }
            await this.createTicket(this.ticket);
        },
        resetForm() {
            this.resetErrors();
            this.ticket = {
                subject: '',
                body: '',
                status: 'pending',
                category: '',
                note: '',
            };
        },
    },
}
</script>
