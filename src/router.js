import Vue from "vue";
import Router from "vue-router";
import Home from "@/views/Home.vue";
import Listar from "@/views/listar.vue";
import Login from "@/views/Login.vue";
import Cadastro from "@/views/Cadastro.vue";
import Erro from "@/views/Erro.vue"; // tela de erro caso não esteja logado

Vue.use(Router);

const router = new Router({
    mode: 'history',
    routes: [{
            path: '/',
            name: 'login',
            component: Login
        },
        {
            path: '/cadastro',
            name: 'cadastro',
            component: Cadastro
        },
        {
            path: '/listar',
            name: 'listar',
            component: Listar,
            meta: { requiresAuth: true }
        },
        {
            path: '/home',
            name: 'home',
            component: Home,
            meta: { requiresAuth: true }
        },
        {
            path: '/erro',
            name: 'erro',
            component: Erro
        }
    ]
});

router.beforeEach((to, from, next) => {
    const usuario = localStorage.getItem('usuario_nome');
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!usuario) {
            next({ name: 'erro' });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;