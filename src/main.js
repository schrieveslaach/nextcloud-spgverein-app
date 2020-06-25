import Vue from 'vue';
import App from './app.vue';
import store from './store.js';
import dayjs from 'dayjs';
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.extend(localizedFormat);

export default new Vue({
	store,
	el: '#content',
	render: h => h(App),
});
