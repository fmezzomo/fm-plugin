import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router';
import store from './store';

import TableTab from './components/TableTab.vue';
import GraphTab from './components/GraphTab.vue';
import SettingsTab from './components/SettingsTab.vue';

import './assets/css/fm-plugin-global.css'; // Import the global CSS file

Vue.use(VueRouter);

const routes = [
  { path: '/table', component: TableTab },
  { path: '/graph', component: GraphTab },
  { path: '/settings', component: SettingsTab },
  { path: '/', redirect: '/table'}
];

const router = new VueRouter({
  routes,
});

Vue.prototype.__ = function (text, domain) {
  return fmPluginData.fmPluginTranslations[text] || text;
};

 new Vue({
  el: "#fm-plugin-app",
  render: (h) => h(App),
  router,
  store,
  //translatedStrings,
  created() {
    this.$store.dispatch('fetchSettings'); // Fetch and initialize settings
    this.$store.dispatch('fetchData'); // Fetch and initialize data
  },
 });