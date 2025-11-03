import Vue from 'vue'
import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import axios from 'axios'

import Toast, { POSITION } from "vue-toastification"
import "vue-toastification/dist/index.css"


Vue.config.productionTip = false


Vue.use(Toast, {
    position: POSITION.TOP_RIGHT,
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    maxToasts: 3,
})

Vue.prototype.$axios = axios

new Vue({
    router,
    render: h => h(App),
}).$mount('#app')