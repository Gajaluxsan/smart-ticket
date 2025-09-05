<template>
    <div class="app-layout">
        <!-- Header -->
        <header class="app-layout__header">
            <div class="app-layout__header-content">
                <!-- Left Title -->
                <div class="app-layout__title">
                    <h1 class="app-layout__title-text">Ticket AI</h1>
                </div>
                
                <!-- Right Navigation -->
                <nav class="app-layout__nav">
                    <ul class="app-layout__nav-list">
                        <li class="app-layout__nav-item">
                            <router-link 
                                to="/dashboard" 
                                class="app-layout__nav-link"
                                :class="{ 'app-layout__nav-link--active': $route.path === '/dashboard' }"
                            >
                                Dashboard
                            </router-link>
                        </li>
                        <li class="app-layout__nav-item">
                            <router-link 
                                to="/tickets" 
                                class="app-layout__nav-link"
                                :class="{ 'app-layout__nav-link--active': $route.path.startsWith('/tickets') }"
                            >
                                Tickets
                            </router-link>
                        </li>
                        <li class="app-layout__nav-item">
                            <router-link 
                                to="/analytics" 
                                class="app-layout__nav-link"
                                :class="{ 'app-layout__nav-link--active': $route.path === '/analytics' }"
                            >
                                Analytics
                            </router-link>
                        </li>
                        <li class="app-layout__nav-item">
                            <router-link 
                                to="/settings" 
                                class="app-layout__nav-link"
                                :class="{ 'app-layout__nav-link--active': $route.path === '/settings' }"
                            >
                                Settings
                            </router-link>
                        </li>
                    </ul>
                </nav>
                
                <!-- Theme Toggle Button -->
                <button 
                    class="theme-toggle" 
                    @click="toggleTheme"
                    :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                >
                    <svg 
                        v-if="isDarkMode" 
                        class="theme-toggle__icon" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg 
                        v-else 
                        class="theme-toggle__icon" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-layout__main">
            <router-view />
        </main>
    </div>
</template>

<script>
export default {
    name: 'AppLayout',
    data() {
        return {
            isDarkMode: false
        }
    },
    mounted() {
        // Check for saved theme preference or default to light mode
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            this.isDarkMode = savedTheme === 'dark';
        } else {
            // Check system preference
            this.isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.applyTheme();
    },
    methods: {
        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            this.applyTheme();
            // Save preference to localStorage
            localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
        },
        applyTheme() {
            const html = document.documentElement;
            if (this.isDarkMode) {
                html.setAttribute('data-theme', 'dark');
            } else {
                html.removeAttribute('data-theme');
            }
        }
    }
}
</script>
