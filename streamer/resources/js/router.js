import { createRouter, createWebHistory } from "vue-router";


import Login from "./Pages/Authentication/Login.vue";
import Register from "./Pages/Authentication/Register.vue";
import Dashboard from "./Pages/Dashboard/Dashboard.vue";
// Daftar route
const routes = [
    { path: "/", redirect: "/login", meta: { title: "Login" } },
    { path: "/login", component: Login, meta: { title: "Login" } },
    { path: "/register", component: Register, meta: { title: "Register" } },
    { path: "/dashboard", component: Dashboard, meta: { title: "Dashboard" } },


];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Set judul halaman
router.afterEach((to) => {
    if (to.meta?.title) {
        document.title = to.meta.title;
    }
});

export default router;
