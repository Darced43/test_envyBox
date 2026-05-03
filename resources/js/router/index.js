import { createRouter, createWebHistory } from 'vue-router';
import CreateView from '../views/CreateView.vue';
import PollView from '../views/PollView.vue';

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'create', component: CreateView },
        { path: '/poll/:code', name: 'poll', component: PollView, props: true },
        { path: '/create', redirect: '/' }
    ]
});