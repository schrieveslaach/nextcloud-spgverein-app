import Members from './members.vue';

import {library} from '@fortawesome/fontawesome-svg-core'
import {faPrint} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import App from './app.vue';

library.add(faPrint);

const Vue = require('./node_modules/vue/dist/vue.js');
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.component('app', App);

new Vue({
    el: '#app-content-wrapper',
    template: '<app></app>'
});
