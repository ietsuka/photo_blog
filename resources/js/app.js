require('./bootstrap');

import Vue from 'vue'
import router from './router'
import store from './store'

Vue.component('app-component', require('./App.vue').default);

const createApp = async () => {
  await store.dispatch('auth/currentUser')
  const app = new Vue({
      el: '#app',
      router,
      store
  })
};

createApp()