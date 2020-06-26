import Vue from 'vue';
import App from './app.vue';
import store from './store.js';
import dayjs from 'dayjs';
import 'dayjs/locale/de';
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.locale('de');
dayjs.extend(localizedFormat);

export default new Vue({
	store,
	el: '#content',
	render: h => h(App),
});
