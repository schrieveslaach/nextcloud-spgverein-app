import Vue from 'vue';
import App from './app.vue';
import createStore from './store';
import router from './router';
import dayjs from 'dayjs';
import 'dayjs/locale/de';
import localizedFormat from 'dayjs/plugin/localizedFormat';

dayjs.locale('de');
dayjs.extend(localizedFormat);

(async() => {
	const store = await createStore();

	return new Vue({
		store,
		router,
		el: '#content',
		render: h => h(App),
	});
})();
