<template>
    <div>
        <div v-if="loading" class="loading">
            <div class="loading__spinner"></div>
            <p>Loading tickets...</p>
        </div>
        <div v-else class="table__container">
            <table class="table">
                <thead class="table__header">
                    <tr>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Confidence</th>
                        <th>Explanation</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="ticket in tickets"
                        :key="ticket.id"
                        class="table__row"
                    >
                        <td class="table__cell table__cell--subject">
                            <router-link
                                :to="`/tickets/${ticket.id}`"
                                class="table__link"
                            >
                                {{ ticket.subject }}
                            </router-link>
                        </td>

                        <td class="table__cell">
                            <span
                                class="badge badge--status"
                                :class="`badge--${ticket.status}`"
                            >
                                {{ ticket.status }}
                            </span>
                        </td>

                        <td class="table__cell">
                            <span
                                v-if="ticket.category"
                                class="badge badge--category"
                            >
                                {{ ticket.category }}
                            </span>
                            <span v-else class="text-muted">-</span>
                        </td>

                        <td class="table__cell">
                            <div
                                v-if="ticket.confidence !== null"
                                class="progress"
                            >
                                <div class="progress__bar">
                                    <div
                                        class="progress__fill"
                                        :style="{
                                            width: `${
                                                ticket.confidence * 100
                                            }%`,
                                        }"
                                    ></div>
                                </div>
                                <span class="progress__text">
                                    {{ Math.round(ticket.confidence * 100) }}%
                                </span>
                            </div>
                            <span v-else class="text-muted">-</span>
                        </td>

                        <td class="table__cell">
                            <div
                                v-if="ticket.explanation"
                                class="tooltip"
                                :title="ticket.explanation"
                            >
                                <svg
                                    class="tooltip__icon"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </div>
                            <span v-else class="text-muted">-</span>
                        </td>

                        <td class="table__cell">
                            <span v-if="ticket.note" class="badge badge--note">
                                Note
                            </span>
                            <span v-else class="text-muted">-</span>
                        </td>

                        <td class="table__cell table__cell--actions">
                            <button
                                class="btn btn--secondary btn--small"
                                @click="classifyTicket(ticket.id)"
                            >
                                Classify
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div v-if="!loading && totalPages > 1" class="pagination">
            <button
                class="btn btn--secondary"
                @click="previousPage"
                :disabled="currentPage === 1"
            >
                Previous
            </button>

            <span class="pagination__info">
                Page {{ currentPage }} of {{ totalPages }}
            </span>

            <button
                class="btn btn--secondary"
                @click="nextPage"
                :disabled="currentPage === totalPages"
            >
                Next
            </button>
        </div>
    </div>
</template>
<script>
export default {
    name: "TicketTable",
    props: {
        tickets: {
            type: Array,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
        total: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        }
    },
    computed: {
        totalPages() {
            return Math.ceil(this.total / this.perPage);
        }
    },
    methods: {
        classifyTicket(id) {
            this.$emit("classifyTicket", id);
        },
        previousPage() {
            if (this.currentPage > 1) {
                this.$emit("goToPage", this.currentPage - 1);
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.$emit("goToPage", this.currentPage + 1);
            }
        },
    },
};
</script>
