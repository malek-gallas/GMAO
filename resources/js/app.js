// Import bootstrap
import './bootstrap';

// Import vue
import { createApp } from 'vue';

// Import vue-router
import { createRouter, createWebHistory } from 'vue-router';

// Create the root app instance using vue
const app = createApp({});

// Define route components
import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

// Define routes
const routes = [
  { path: '/intervenants', component: ExampleComponent },
  { path: '/fabricants', component: ExampleComponent },
  { path: '/machines', component: ExampleComponent },
  { path: '/interventions', component: ExampleComponent },
  { path: '/corrections', component: ExampleComponent },
  { path: '/planning', component: ExampleComponent },
  { path: '/statistiques', component: ExampleComponent },
];

// Create the router instance
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Make the app use the router
app.use(router);

// Mount the app
app.mount('#app');
