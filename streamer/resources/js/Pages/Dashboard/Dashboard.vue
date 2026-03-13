<template>
  <div class="flex min-h-screen bg-gray-50/50">
    <Sidebar :activeMenu="activeMenu" @update:activeMenu="activeMenu = $event" />

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
      
      <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-900">Ringkasan Statistik</h1>
          <p class="text-xs text-gray-500 font-medium">Selamat datang kembali, Admin!</p>
        </div>
        
        <div class="flex items-center gap-4">
          <button class="p-2 text-gray-400 hover:text-blue-600 transition-colors relative">
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            <i class="fas fa-bell"></i>
          </button>
          <div class="h-8 w-px bg-gray-100"></div>
          <span class="text-sm font-semibold text-gray-700 hidden sm:block">{{ currentDate }}</span>
        </div>
      </header>

      <div class="p-6 overflow-y-auto custom-scrollbar">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="(stat, index) in stats" :key="index" 
               class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-4">
              <div :class="stat.iconBg" class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110">
                <i :class="[stat.icon, stat.iconColor]" class="text-xl"></i>
              </div>
              <span :class="stat.trendColor" class="text-[10px] font-bold px-2 py-1 rounded-full bg-opacity-10">
                {{ stat.trend }}
              </span>
            </div>
            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">{{ stat.title }}</h3>
            <p class="text-2xl font-black text-gray-900">{{ stat.value }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900">Jadwal Stream Mendatang</h2>
                <router-link to="/schedules" class="text-blue-600 text-xs font-bold hover:underline">Lihat Semua</router-link>
              </div>
              
              <div class="space-y-4">
                <div v-if="loading" class="text-center py-10">
                  <div class="animate-spin inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full"></div>
                  <p class="text-xs text-gray-500 mt-2 font-bold uppercase tracking-tighter">Memuat Jadwal...</p>
                </div>

                <div v-else-if="schedules.length === 0" class="text-center py-10 bg-gray-50 rounded-xl border border-dashed">
                  <p class="text-sm text-gray-500 italic">Belum ada jadwal streaming yang direncanakan.</p>
                </div>

                <div v-for="item in schedules" :key="item.id" 
                     class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:border-blue-100 transition-all group">
                  <div class="text-center min-w-[50px]">
                    <p class="text-xs font-bold text-blue-600 uppercase">{{ formatDay(item.start_time) }}</p>
                    <p class="text-lg font-black text-gray-900 leading-none">{{ formatDate(item.start_time) }}</p>
                  </div>
                  <div class="h-10 w-px bg-gray-200"></div>
                  <div class="flex-grow">
                    <h4 class="text-sm font-bold text-gray-800">{{ item.title || 'Untitled Stream' }}</h4>
                    <p class="text-xs text-gray-500">
                      {{ formatTime(item.start_time) }} • 
                      <span :class="statusClass(item.status)" class="font-bold uppercase tracking-tighter text-[9px]">
                        {{ item.status }}
                      </span>
                    </p>
                  </div>
                  <router-link :to="`/schedules/${item.id}`" class="opacity-0 group-hover:opacity-100 bg-blue-600 text-white text-[10px] font-bold px-3 py-2 rounded-lg transition-all">
                    MANAGE
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-2xl shadow-lg shadow-blue-100 text-white">
              <h2 class="text-lg font-bold mb-2">Quick Action</h2>
              <p class="text-xs text-blue-100 mb-6 opacity-80">Mulai manajemen stream atau tambah player baru secara instan.</p>
              <div class="grid grid-cols-2 gap-3">
                <router-link to="/schedules/create" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition-all border border-white/10">
                  <p class="text-[10px] font-bold uppercase tracking-tighter">New Stream</p>
                </router-link>
                <router-link to="/players/create" class="bg-white/10 hover:bg-white/20 p-3 rounded-xl text-center transition-all border border-white/10">
                  <p class="text-[10px] font-bold uppercase tracking-tighter">Add Player</p>
                </router-link>
              </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest">Active Players</h2>
              <div class="space-y-4">
                <div v-for="player in onlinePlayers" :key="player.id" class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-gray-200">
                      <img :src="player.avatar" class="w-full h-full object-cover" />
                    </div>
                    <span class="text-xs font-bold text-gray-700">{{ player.name }}</span>
                  </div>
                  <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
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
      stats: [
        { title: "Total Matches", value: "0", trend: "+0%", icon: "fas fa-gamepad", iconBg: "bg-blue-50", iconColor: "text-blue-600", trendColor: "text-green-600 bg-green-500" },
        { title: "Active Players", value: "0", trend: "+0%", icon: "fas fa-users", iconBg: "bg-purple-50", iconColor: "text-purple-600", trendColor: "text-green-600 bg-green-500" },
        { title: "Stream Hours", value: "0h", trend: "0%", icon: "fas fa-clock", iconBg: "bg-orange-50", iconColor: "text-orange-600", trendColor: "text-red-600 bg-red-500" },
        { title: "Avg. Viewers", value: "0", trend: "+0%", icon: "fas fa-chart-line", iconBg: "bg-green-50", iconColor: "text-green-600", trendColor: "text-green-600 bg-green-500" },
      ],
      schedules: [],
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
    async fetchSchedules() {
      this.loading = true;
      try {
        const response = await axios.get('/api/v1/schedules');
        this.schedules = response.data.data;
        this.schedules = this.schedules.slice(0, 3);
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
      switch(status) {
        case 'live': return 'text-red-600';
        case 'scheduled': return 'text-blue-600';
        case 'finished': return 'text-gray-400';
        default: return 'text-orange-500';
      }
    }
  }
}
</script>