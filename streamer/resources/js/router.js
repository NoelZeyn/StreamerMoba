import { createRouter, createWebHistory } from "vue-router";

const routes = [
    { path: "/", redirect: "/login", meta: { title: "Login" } },

    {
        path: "/login",
        component: () => import("./Pages/Authentication/Login.vue"),
        meta: { title: "Login" }
    },
    {
        path: "/register",
        component: () => import("./Pages/Authentication/Register.vue"),
        meta: { title: "Register" }
    },
    {
        path: "/dashboard",
        component: () => import("./Pages/Dashboard/Dashboard.vue"),
        meta: { title: "Dashboard" }
    },
    {
        path: "/schedules/:id",
        component: () => import("./Pages/Schedule/ScheduleDetail.vue"),
        meta: { title: "Schedule Detail" }
    },
    {
        path: "/schedules/create",
        component: () => import("./Pages/Schedule/ScheduleCreate.vue"),
        meta: { title: "Create Schedule" }
    },
    {
        path: "/matches/:id/create",
        component: () => import("./Pages/Play/MatchCreate.vue"),
        meta: { title: "Create Match" }
    },
    {
        path: "/players/create",
        component: () => import("./Pages/Player/PlayerCreate.vue"),
        meta: { title: "Create Player" }
    },
    {
        path: "/donations",
        component: () => import("./Pages/Donation/DonationIndex.vue"),
        meta: { title: "Donations" }
    },
    {
        path: "/queue",
        component: () => import("./Pages/Play/Queue.vue"),
        meta: { title: "Queue" }
    },
    {
        path: "/queues/:id",
        component: () => import("./Pages/Play/AdminQueue.vue"),
        meta: { title: "Queue List" }
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/login"
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.afterEach((to) => {
    if (to.meta?.title) {
        document.title = to.meta.title;
    }
});

export default router;