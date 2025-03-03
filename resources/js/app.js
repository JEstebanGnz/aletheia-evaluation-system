require('./bootstrap');

// Import modules...
import Vue from 'vue';
import {App as InertiaApp, plugin as InertiaPlugin} from '@inertiajs/inertia-vue';
import PortalVue from 'portal-vue';
//Vuetify config
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify)

import es from 'vuetify/lib/locale/es'

export default new Vuetify({
    lang: {
        locales: {es},
        current: 'es'
    },

})

Vue.mixin({methods: {route}});
Vue.use(InertiaPlugin);
Vue.use(PortalVue);


const app = document.getElementById('app');




new Vue({
    vuetify: new Vuetify(),
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
