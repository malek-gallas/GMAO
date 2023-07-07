// Import bootstrap
import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.js';

// Import vue
import { createApp } from 'vue';
const app = createApp({});

// Import v-form
import Form from 'vform';
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

//Import Gate (Front End)
import Gate from "./Gate";
app.config.globalProperties.$gate = new Gate(window.user);

// Import vue-router
import { createRouter, createWebHistory } from 'vue-router';

// Define route components
import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
import Users from './components/Users.vue';
app.component('Users', Users);
import Error from './components/Error.vue';
app.component('Error', Error);


// Define routes
const routes = [
  { path: '/Users', component: Users },
  { path: '/fabricants', component: ExampleComponent },
  { path: '/machines', component: ExampleComponent },
  { path: '/interventions', component: ExampleComponent },
  { path: '/corrections', component: ExampleComponent },
  { path: '/planning', component: ExampleComponent },
  { path: '/statistiques', component: ExampleComponent },
  { path: '/home', component: ExampleComponent},
  { path: '/:pathMatch(.*)*', component: Error},
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
