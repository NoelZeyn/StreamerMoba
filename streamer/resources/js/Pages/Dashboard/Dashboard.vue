<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'dashboard'" />

    <main class="flex-1 min-w-0 flex flex-col overflow-hidden">

      <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between shrink-0">
        <div>
          <h1 class="text-xl font-bold text-gray-900">Ringkasan Statistik</h1>
          <p class="text-xs text-gray-500 font-medium italic">Selamat datang kembali, Admin!</p>
        </div>

        <div class="flex items-center gap-4">
          <span class="text-sm font-semibold text-gray-700 hidden sm:block">{{ currentDate }}</span>
          <div class="h-8 w-px bg-gray-100"></div>
          <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
            <i class="fas fa-bell"></i>
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="(stat, index) in stats" :key="index"
            class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all group">

            <div class="flex items-center justify-between mb-4">
              <div :class="stat.iconBg"
                class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i :class="[stat.icon, stat.iconColor]" class="text-xl"></i>
              </div>

              <span :class="stat.trendColor" class="text-[10px] font-bold px-2 py-1 rounded-full bg-opacity-10">
                {{ stat.trend }}
              </span>
            </div>

            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">
              {{ stat.title }}
            </h3>

            <p class="text-2xl font-black text-gray-900">
              {{ stat.value }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <!-- SCHEDULE LIST -->
          <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">

              <div class="flex items-center justify-between mb-6">
                <div>
                  <h2 class="text-lg font-bold text-gray-900 italic uppercase tracking-tighter">
                    Recent Schedules
                  </h2>
                  <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    Page {{ pagination.current_page }}
                  </p>
                </div>

                <div class="flex items-center gap-2">

                  <button @click="fetchSchedules(pagination.prev_page_url)"
                    :disabled="!pagination.prev_page_url || loading"
                    class="p-2 bg-gray-50 rounded-lg hover:bg-blue-50 disabled:opacity-30 transition-colors border border-gray-100">
                    <i class="fas fa-chevron-left text-xs text-gray-600"></i>
                  </button>

                  <button @click="fetchSchedules(pagination.next_page_url)"
                    :disabled="!pagination.next_page_url || loading"
                    class="p-2 bg-gray-50 rounded-lg hover:bg-blue-50 disabled:opacity-30 transition-colors border border-gray-100">
                    <i class="fas fa-chevron-right text-xs text-gray-600"></i>
                  </button>

                </div>
              </div>

              <div class="space-y-4">

                <!-- LOADING -->
                <div v-if="loading" class="flex flex-col items-center py-12">
                  <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                  <p class="text-[10px] font-bold text-gray-400 mt-4 uppercase tracking-widest">
                    Synchronizing Data...
                  </p>
                </div>

                <!-- EMPTY -->
                <div v-else-if="schedules.length === 0"
                  class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                  <p class="text-sm text-gray-500 italic">
                    No stream schedules found.
                  </p>
                </div>

                <!-- LIST -->
                <div v-else v-for="item in schedules" :key="item.id"
                  class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:border-blue-200 transition-all group">

                  <div class="text-center min-w-[55px]">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">
                      {{ formatDay(item.start_time) }}
                    </p>
                    <p class="text-xl font-black text-gray-900 leading-none">
                      {{ formatDate(item.start_time) }}
                    </p>
                  </div>

                  <div class="h-10 w-px bg-gray-200"></div>

                  <div class="flex-grow min-w-0">
                    <h4 class="text-sm font-bold text-gray-800 truncate uppercase tracking-tight">
                      {{ item.title || 'Untitled Session' }}
                    </h4>

                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-[10px] text-gray-500 font-medium">
                        {{ formatTime(item.start_time) }}
                      </span>

                      <span class="h-1 w-1 bg-gray-300 rounded-full"></span>

                      <span :class="statusClass(item.status)" class="font-black uppercase tracking-tighter text-[9px]">
                        {{ item.status }}
                      </span>
                    </div>
                  </div>

                  <router-link :to="`/schedules/${item.id}`"
                    class="opacity-0 group-hover:opacity-100 bg-blue-600 text-white text-[10px] font-bold px-4 py-2 rounded-lg transition-all shadow-lg shadow-blue-100">
                    MANAGE
                  </router-link>
                </div>

              </div>

              <!-- PAGINATION -->
              <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">

                <p class="text-xs text-gray-400 font-semibold">
                  Showing
                  {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                  -
                  {{ (pagination.current_page - 1) * pagination.per_page + schedules.length }}
                  of {{ pagination.total }}
                </p>

                <div class="flex items-center gap-2">

                  <button @click="fetchSchedules(`/api/v1/schedules?page=${pagination.current_page - 1}`)"
                    :disabled="pagination.current_page === 1"
                    class="px-3 py-1 text-xs font-bold border rounded-lg disabled:opacity-30 hover:bg-gray-50">
                    Prev
                  </button>

                  <button v-for="page in pagination.last_page" :key="page"
                    @click="fetchSchedules(`/api/v1/schedules?page=${page}`)" :class="[
                      'px-3 py-1 text-xs font-bold rounded-lg border',
                      page === pagination.current_page
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'hover:bg-gray-50'
                    ]">
                    {{ page }}
                  </button>

                  <button @click="fetchSchedules(`/api/v1/schedules?page=${pagination.current_page + 1}`)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 text-xs font-bold border rounded-lg disabled:opacity-30 hover:bg-gray-50">
                    Next
                  </button>

                </div>
              </div>

            </div>
          </div>

          <!-- RIGHT PANEL -->
          <div class="space-y-6">

            <!-- QUICK ACTION -->
            <div class="bg-gray-900 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
              <div class="relative z-10">
                <h2 class="text-lg font-bold mb-1 italic">Quick Action</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-6 opacity-70">Shortcuts</p>

                <div class="grid grid-cols-2 gap-3">

                  <router-link to="/schedules/create"
                    class="bg-white/5 hover:bg-white/10 p-4 rounded-xl text-center border border-white/10 transition-all">
                    <i class="fas fa-plus-circle mb-2 block text-blue-400"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest">
                      New Stream
                    </p>
                  </router-link>

                  <router-link to="/players/create"
                    class="bg-white/5 hover:bg-white/10 p-4 rounded-xl text-center border border-white/10 transition-all">
                    <i class="fas fa-user-plus mb-2 block text-purple-400"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest">
                      Add Player
                    </p>
                  </router-link>

                </div>
              </div>
            </div>

            <!-- ACTIVE PLAYERS -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <h2
                class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-green-500 pl-3">
                Active Players
              </h2>

              <div class="space-y-5">

                <div v-for="player in onlinePlayers" :key="player.id" class="flex items-center justify-between group">

                  <div class="flex items-center gap-3">

                    <div
                      class="w-9 h-9 rounded-full bg-gray-100 p-0.5 border-2 border-transparent group-hover:border-green-500 transition-all">
                      <img :src="player.avatar" class="w-full h-full rounded-full object-cover" />
                    </div>

                    <span class="text-xs font-bold text-gray-700 uppercase tracking-tight">
                      {{ player.name }}
                    </span>

                  </div>

                  <div class="flex flex-col items-end">

                    <span
                      class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.8)] animate-pulse"></span>

                    <span class="text-[8px] text-gray-300 font-black mt-1 uppercase">
                      Live
                    </span>

                  </div>

                </div>

              </div>
            </div>

          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue';
import axios from 'axios';

export default {
  components: { Sidebar },
  data() {
    return {
      activeMenu: "dashboard",
      loading: true,
      currentDate: new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(new Date()),
      schedules: [],
      pagination: {
        next_page_url: null,
        prev_page_url: null
      },
      currentPage: 1,
      stats: [
        { title: "Total Matches", value: "1,284", trend: "+12%", icon: "fas fa-gamepad", iconBg: "bg-blue-50", iconColor: "text-blue-600", trendColor: "text-green-600 bg-green-50" },
        { title: "Active Players", value: "482", trend: "+5%", icon: "fas fa-users", iconBg: "bg-purple-50", iconColor: "text-purple-600", trendColor: "text-green-600 bg-green-50" },
        { title: "Stream Hours", value: "156h", trend: "-2%", icon: "fas fa-clock", iconBg: "bg-orange-50", iconColor: "text-orange-600", trendColor: "text-red-600 bg-red-50" },
        { title: "Avg. Viewers", value: "892", trend: "+18%", icon: "fas fa-chart-line", iconBg: "bg-green-50", iconColor: "text-green-600", trendColor: "text-green-600 bg-green-50" },
      ],
      onlinePlayers: [
        { id: 1, name: "Vasta_Player1", avatar: "https://i.pravatar.cc/150?u=1" },
        { id: 2, name: "Stream_King", avatar: "https://i.pravatar.cc/150?u=2" },
        { id: 3, name: "Zero_Kelvin", avatar: "https://i.pravatar.cc/150?u=3" },
      ]
    }
  },
  mounted() {
    this.fetchSchedules();
  },
  methods: {
    async fetchSchedules(url = '/api/v1/schedules') {
      this.loading = true;

      try {
        const response = await axios.get(url);

        const result = response.data.data;

        this.schedules = result.data;

        this.pagination.current_page = result.current_page;
        this.pagination.last_page = result.last_page;
        this.pagination.per_page = result.per_page;
        this.pagination.total = result.total;

        this.pagination.next_page_url = result.next_page_url;
        this.pagination.prev_page_url = result.prev_page_url;

        this.currentPage = result.current_page;

      } catch (error) {
        console.error("Error fetching schedules:", error);
      } finally {
        this.loading = false;
      }
    },
    formatDay(dateStr) {
      const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
      return days[new Date(dateStr).getDay()];
    },
    formatDate(dateStr) {
      return new Date(dateStr).getDate();
    },
    formatTime(dateStr) {
      return new Date(dateStr).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
      }) + ' WIB';
    },
    statusClass(status) {
      switch (status) {
        case 'live': return 'text-red-600';
        case 'scheduled': return 'text-blue-600';
        case 'finished': return 'text-gray-400';
        default: return 'text-orange-500';
      }
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
</style>