import {library} from '@fortawesome/fontawesome-svg-core'
import {faFile, faFilePdf, faPrint, faTimes} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import App from './app.vue';
import Modal from './modal.vue';

library.add(faFile);
library.add(faFilePdf);
library.add(faPrint);
library.add(faTimes);

const Vue = require('./node_modules/vue/dist/vue.js');
Vue.component('modal', Modal);
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.component('app', App);

new Vue({
    el: '#app-content-wrapper',
    template: '<app></app>'
});
