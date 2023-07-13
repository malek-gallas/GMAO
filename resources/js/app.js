/* ----------------------------------- Import vue & Create app ------------------------------------*/
import { createApp } from 'vue';
const app = createApp({
  data() {
      return {
          search: '',
      };
  },
  methods: {
    handleSearch: _.debounce(function() {
      emitter.emit('searching', this.search);
    }, 500),
  }
});
/* ------------------------------------------ Bootstrap ------------------------------------------*/
import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.js';

/* ----------------------------------------- Pagination ------------------------------------------*/
import { Bootstrap5Pagination } from 'laravel-vue-pagination';
app.component('Bootstrap5Pagination', Bootstrap5Pagination)

/* --------------------------------------- Progress Bar ------------------------------------------*/
import VueProgressBar from "@aacassandra/vue3-progressbar";
const options = {
  color: "#bffaf3",
  failedColor: "#874b4b",
  thickness: "4px",
  transition: {
    speed: "0.2s",
    opacity: "0.6s",
    termination: 300,
  },
  autoRevert: true,
  location: "left",
  inverse: false,
};

/* ---------------------------------------- Sweet Alert ------------------------------------------*/
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

/* ------------------------------------------ Gate.js ------------------------------------------*/
import Gate from "./Gate";
app.config.globalProperties.$gate = new Gate(window.user);

/* ---------------------------------------- Sheet.js ------------------------------------------*/
import XLSX from 'xlsx';
app.config.globalProperties.$xlsx = XLSX;

/* ------------------------------------ Custom event (mitt) --------------------------------------*/
import mitt from 'mitt';
const emitter = mitt()
app.config.globalProperties.$emitter = emitter;

/* ------------------------------------ Data Binding (VueX) --------------------------------------*/
import store from './Store'

/* ----------------------------------------- Vue Router ------------------------------------------- */

// Import vue-router
import { createRouter, createWebHistory } from 'vue-router';

// Define components
import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
import Users from './components/Users.vue';
app.component('Users', Users);
import Suppliers from './components/Suppliers.vue';
app.component('Suppliers', Suppliers);
import Machines from './components/Machines.vue';
app.component('Machines', Machines);
import Error from './components/Error.vue';
app.component('Error', Error);

// Define routes
const routes = [
  { path: '/Utilisateurs', component: Users },
  { path: '/Fournisseurs', component: Suppliers },
  { path: '/Machines', component: Machines },
  { path: '/Preventions', component: ExampleComponent },
  { path: '/Corrections', component: ExampleComponent },
  { path: '/Planning', component: ExampleComponent },
  { path: '/Statistiques', component: ExampleComponent },
  { path: '/home', component: ExampleComponent},
  { path: '/:pathMatch(.*)*', redirect: '/home' }, // Redirect all other paths to '/home'
];

// Create the router instance
const router = createRouter({
  history: createWebHistory(),
  routes,
});

/* -------------------------------------------- Mount ------------------------------------------ */
app.use(router).use(VueProgressBar, options).use(VueSweetalert2).use(store);
app.mount('#app');

