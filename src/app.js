import { createApp } from 'vue'
import router from './router'
import './assets/app.css'
import App from './App.vue'

const app = createApp(App);

app.use(router);
app.mount('#app')