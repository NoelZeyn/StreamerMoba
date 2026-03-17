<template>
  <div>
    <button
      class="fixed top-4 left-4 z-[1001] bg-white border border-gray-200 p-2 rounded-lg shadow-md text-blue-600 md:hidden"
      @click="toggleSidebar">
      <span v-if="!isSidebarOpen">☰</span>
      <span v-else>✕</span>
    </button>

    <div v-if="isSidebarOpen && isMobile" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[999]"
      @click="toggleSidebar"></div>

    <aside :class="[
      'transition-all duration-300 z-[1000] fixed md:sticky top-0 left-0 h-screen w-[280px] bg-white border-r border-gray-100 flex flex-col shadow-sm',
      { '-translate-x-full': !isSidebarOpen && isMobile },
    ]">

      <div class="p-6 mb-2">
        <div class="flex items-center gap-3 px-2">
          <div class="bg-blue-600 p-1.5 rounded-lg shadow-blue-200 shadow-lg">

          </div>
          <div class="flex flex-col">
            <span class="text-sm font-black text-gray-900 leading-none tracking-tight uppercase">Sistem Stream</span>
            <span class="text-[10px] text-blue-600 font-bold tracking-widest uppercase">Vasta</span>
          </div>
        </div>
      </div>

      <div class="flex-grow overflow-y-auto px-4 custom-scrollbar">

        <div class="mb-6">
          <p class="px-4 text-[11px] font-extrabold text-blue-400 uppercase tracking-[0.2em] mb-3">
            Menu Utama
          </p>
          <ul class="space-y-1">
            <router-link to="/dashboard" v-slot="{ isActive }">
              <li :class="menuClass(isActive || activeMenu === 'dashboard')" @click="setActive('dashboard')">
                <img src="@/assets/dashboard.svg" class="w-5 h-5 opacity-70"
                  :class="{ 'brightness-0 invert-0': isActive }" />
                <span>Dashboard</span>
              </li>
            </router-link>

            <router-link to="/schedules/create" v-slot="{ isActive }">
              <li :class="menuClass(isActive)">
                <img src="@/assets/laporan1.svg" class="w-5 h-5 opacity-70" />
                <span>Schedule</span>
              </li>
            </router-link>

            <router-link to="/players/create" v-slot="{ isActive }">
              <li :class="menuClass(isActive)">
                <img src="@/assets/profil.svg" class="w-5 h-5 opacity-70" />
                <span>Player</span>
              </li>
            </router-link>


          </ul>
        </div>

        <div class="mb-6">
          <p class="px-4 text-[11px] font-extrabold text-blue-400 uppercase tracking-[0.2em] mb-3">
            Saweria
          </p>
          <ul class="space-y-1">
            <router-link to="/donations" v-slot="{ isActive }">
              <li :class="menuClass(isActive)">
                <img src="@/assets/folder.svg" class="w-5 h-5 opacity-70" />
                <span>Donation</span>
              </li>
            </router-link>
            <router-link to="/saweria" v-slot="{ isActive }">
              <li :class="menuClass(isActive)">
                <img src="@/assets/folder.svg" class="w-5 h-5 opacity-70" />
                <span>Saweria Integration</span>
              </li>
            </router-link>
          </ul>
        </div>

      </div>

      <div class="p-4 border-t border-gray-50 bg-gray-50/50">
        <!-- <router-link to="/profile" class="block mb-2 group">
          <div :class="menuClass(activeMenu === 'profile')" class="bg-transparent border-none">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs border border-blue-200">
              U
            </div>
            <div class="flex flex-col">
              <span class="text-xs font-bold text-gray-800 hover:text-blue-600 ">User Profile</span>
              <span class="text-[10px] text-gray-500">Lihat Pengaturan</span>
            </div>
          </div>
        </router-link> -->

        <button @click="showModalConfirm = true"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 transition-all group cursor-pointer">
          <img src="@/assets/SignOut.svg" class="w-5 h-5 transition-transform group-hover:translate-x-1" />
          <span>Keluar Aplikasi</span>
        </button>
      </div>

      <ModalConfirm :visible="showModalConfirm" title="Konfirmasi Logout"
        message="Sesi Anda akan berakhir. Ingin keluar?" @confirm="logout" @cancel="showModalConfirm = false" />
    </aside>
  </div>
</template>

<script>
import axios from "axios";
import Logo from "@/assets/PLN.svg";
import ModalConfirm from "@/components/ModalConfirm.vue";

export default {
  name: "Sidebar",
  components: { ModalConfirm },
  props: ["activeMenu"],
  data() {
    return {
      Logo,
      showModalConfirm: false,
      isSidebarOpen: true,
      isMobile: window.innerWidth < 768,
    };
  },
  mounted() {
    this.checkScreenSize();
    window.addEventListener("resize", this.checkScreenSize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkScreenSize);
  },
  methods: {
    setActive(menu) {
      this.$emit("update:activeMenu", menu);
    },
    logout() {
      const token = localStorage.getItem("token");
      axios.post("http://localhost:8000/api/v1/logout", {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
        .then(() => {
          localStorage.clear();
          this.$router.push("/login");
        })
        .catch(() => {
          localStorage.clear();
          this.$router.push("/login");
        });
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    checkScreenSize() {
      this.isMobile = window.innerWidth < 768;
      this.isSidebarOpen = !this.isMobile;
    },
    menuClass(isActive) {
      return [
        "flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all duration-200 cursor-pointer group",
        isActive
          ? "bg-blue-50 text-blue-700 font-bold shadow-sm shadow-blue-100/50"
          : "text-gray-500 hover:bg-gray-50 hover:text-gray-900"
      ];
    },
  },
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>