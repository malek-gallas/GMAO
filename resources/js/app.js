// Import bootstrap
import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.js';

// Import vue
import { createApp } from 'vue';
// Create the root app instance using vue
const app = createApp({});

// Import vue-router
import { createRouter, createWebHistory } from 'vue-router';

// Import v-form
import Form from 'vform';
// Create the form instance
const form = new Form();

// Import VueProgressBar
import VueProgressBar from "@aacassandra/vue3-progressbar";
const options = {
  color: "#bffaf3",
  failedColor: "#874b4b",
  thickness: "5px",
  transition: {
    speed: "0.2s",
    opacity: "0.6s",
    termination: 300,
  },
  autoRevert: true,
  location: "left",
  inverse: false,
};

// Import sweetalert2
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// Define route components
import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
import Users from './components/Users.vue';
app.component('Users', Users);

// Define routes
const routes = [
  { path: '/Users', component: Users },
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

// Make the app use the instances
app.use(router);
app.use(form);
app.use(VueProgressBar, options);
app.use(VueSweetalert2);

// Mount the app
app.mount('#app');
