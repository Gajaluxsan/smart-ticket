<template>
    <div class="dashboard">
        <div class="dashboard__header">
            <h1 class="dashboard__title">Dashboard</h1>
            <p class="dashboard__subtitle">Overview of your ticket system</p>
        </div>
        <div v-if="loading" class="loading">
            <div class="loading__spinner"></div>
            <p>Loading dashboard data...</p>
        </div>
        <div v-else class="dashboard__content">
            <div class="stats-grid">
                <!-- {{ dashboardStats?.statuses }} -->
                <div class="stat-card stat-card--primary">
                    <div class="stat-card__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Total Tickets</h3>
                        <p class="stat-card__value">{{ dashboardStats?.total || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card--success">
                    <div class="stat-card__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Open Tickets</h3>
                        <p class="stat-card__value">{{ dashboardStats?.statusesCount?.open || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card--warning">
                    <div class="stat-card__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">In Progress</h3>
                        <p class="stat-card__value">{{ dashboardStats?.statusesCount?.in_progress || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card--info">
                    <div class="stat-card__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Closed Tickets</h3>
                        <p class="stat-card__value">{{ dashboardStats?.statusesCount?.closed || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card stat-card--secondary">
                    <div class="stat-card__icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Pending</h3>
                        <p class="stat-card__value">{{ dashboardStats?.statusesCount?.pending || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-card__header">
                        <h3 class="chart-card__title">Ticket Status Distribution</h3>
                        <p class="chart-card__subtitle">Breakdown by status</p>
                    </div>
                    <div class="chart-card__content">
                        <Bar
                        id="my-chart-id-statuses"
                        :options="chartOptions"
                        :data="{
                            labels: dashboardStats?.statuses?.labels,
                            datasets: dashboardStats?.statuses?.datasets,
                            total: dashboardStats?.statuses?.total,
                        }"
                        />
                    </div>
                </div>

                <div class="chart-card" >
                    <div class="chart-card__header">
                        <h3 class="chart-card__title">Categories Overview</h3>
                        <p class="chart-card__subtitle">Tickets by category</p>
                    </div>
                    <div class="chart-card__content">
                        <Bar
                        id="my-chart-id-categories"
                        :options="chartOptions"
                        :data="{
                            labels: dashboardStats?.categories?.labels,
                            datasets: dashboardStats?.categories?.datasets,
                            total: dashboardStats?.categories?.total,
                        }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'pinia';
import { useTicketStore } from '../stores/ticket';
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: 'Dashboard',
    data() {
        return {
            loading: false,
            error: null,
            chartOptions: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    }
            }
            },
        }
    },
    components: {
        Bar,
    },
    computed: {
        ...mapState(useTicketStore, {
            dashboardStats: 'dashboardStats',
        }),
    },
    async mounted() {
        // await this.loadDashboardData();
        await this.getDashboardStats();
    },
    methods: {
        ...mapActions(useTicketStore, {
            getDashboardStats: 'getDashboardStats',
            openCreateModal: 'openCreateModal',
        }),
        async loadDashboardData() {
            this.loading = true;
            this.error = null;
            try {
                await this.getDashboardStats();
            } catch (error) {
                this.error = 'Failed to load dashboard statistics. Please try again.';
                console.error('Dashboard loading error:', error);
            } finally {
                this.loading = false;
            }
        },
        formatStatus(status) {
            return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        },
        getStatusPercentage(count) {
            const total = this.dashboardStats?.statuses?.total || 1;
            return Math.round((count / total) * 100);
        },
        getCategoryPercentage(count) {
            const total = this.dashboardStats?.statuses?.total || 1;
            return Math.round((count / total) * 100);
        }
    }
}
</script>

<style scoped>
/* Dashboard Layout */
.dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.dashboard__header {
    margin-bottom: 2rem;
}

.dashboard__title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    background: linear-gradient(135deg, var(--accent-color), #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.dashboard__subtitle {
    color: var(--text-secondary);
    font-size: 1.125rem;
    margin: 0;
}

.dashboard__content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

/* Stat Cards */
.stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--accent-color);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-color: var(--accent-color);
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card--primary::before { background: #3b82f6; }
.stat-card--success::before { background: #10b981; }
.stat-card--warning::before { background: #f59e0b; }
.stat-card--info::before { background: #06b6d4; }
.stat-card--secondary::before { background: #6b7280; }
.stat-card--accent::before { background: #8b5cf6; }

.stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-card--primary .stat-card__icon {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.stat-card--success .stat-card__icon {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.stat-card--warning .stat-card__icon {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.stat-card--info .stat-card__icon {
    background: rgba(6, 182, 212, 0.1);
    color: #06b6d4;
}

.stat-card--secondary .stat-card__icon {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
}

.stat-card--accent .stat-card__icon {
    background: rgba(139, 92, 246, 0.1);
    color: #8b5cf6;
}

.stat-card__icon svg {
    width: 1.5rem;
    height: 1.5rem;
}

.stat-card__content {
    flex: 1;
}

.stat-card__title {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    margin: 0 0 0.25rem 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-card__value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1;
}

/* Charts Section */
.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.chart-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.chart-card__header {
    margin-bottom: 1.5rem;
}

.chart-card__title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.chart-card__subtitle {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin: 0;
}

/* Chart Card Content */
.chart-card__content {
    padding: 0;
}

/* Quick Actions */
.quick-actions {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    padding: 1.5rem;
}

.quick-actions__title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 1.5rem 0;
}

.quick-actions__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-action:hover {
    background: var(--bg-tertiary);
    border-color: var(--accent-color);
    transform: translateY(-1px);
}

.quick-action__icon {
    width: 2.5rem;
    height: 2.5rem;
    background: var(--accent-color);
    color: white;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.quick-action__icon svg {
    width: 1.25rem;
    height: 1.25rem;
}

.quick-action__content h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: var(--text-primary);
}

.quick-action__content p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Error State */
.error-state {
    text-align: center;
    padding: 3rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 1rem;
}

.error-state__icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.error-state h3 {
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.error-state p {
    color: var(--text-secondary);
    margin: 0 0 1.5rem 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard {
        padding: 1rem;
    }
    
    .dashboard__title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .quick-actions__grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-card__value {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .stat-card__icon {
        width: 2.5rem;
        height: 2.5rem;
    }
}
</style>