import Vue from 'vue';
import App from './app.vue';
import store from './store.js';

export default new Vue({
	store,
	el: '#content',
	render: h => h(App),
});
