<template>
  <div class="flex min-h-screen bg-gray-50/50">
    <Sidebar :activeMenu="'schedules'" />

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">{{ schedule?.title || 'Loading...' }}</h1>
            <p class="text-xs text-gray-500 font-medium">Detail Jadwal Streaming</p>
          </div>
        </div>
        <div v-if="schedule" :class="statusBadge(schedule.status)" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
          {{ schedule.status }}
        </div>
      </header>

      <div v-if="loading" class="flex-1 flex items-center justify-center">
        <div class="animate-spin w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="schedule" class="p-6 overflow-y-auto custom-scrollbar">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest italic">Streaming Info</h2>
              <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                  <p class="text-[10px] font-bold text-gray-400 uppercase">Waktu Mulai</p>
                  <p class="text-sm font-bold text-gray-800">{{ formatFullDate(schedule.start_time) }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                  <p class="text-[10px] font-bold text-gray-400 uppercase">Host ID</p>
                  <p class="text-sm font-bold text-gray-800">User #{{ schedule.user_id }}</p>
                </div>
              </div>
              <div class="mt-4 p-4 border border-gray-100 rounded-xl">
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Catatan</p>
                <p class="text-sm text-gray-600">{{ schedule.notes || 'Tidak ada catatan.' }}</p>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest italic">Match History ({{ schedule.matches.length }})</h2>
                <button class="text-[10px] font-bold bg-blue-600 text-white px-3 py-1.5 rounded-lg">ADD MATCH</button>
              </div>

              <div class="divide-y divide-gray-50">
                <div v-for="(match, index) in schedule.matches" :key="match.id" class="p-6 hover:bg-gray-50/50 transition-colors">
                  <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black text-blue-600">MATCH #{{ index + 1 }}</span>
                    <span class="text-[10px] text-gray-400 font-medium italic">{{ match.played_at }}</span>
                  </div>
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <div v-for="mp in match.players" :key="mp.id" class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden">
                      <div class="absolute top-0 right-0 w-1 h-full bg-blue-500"></div>
                      <p class="text-[10px] font-black text-gray-800 truncate">ID: {{ mp.player_id }}</p>
                      <div class="mt-2 flex flex-col gap-1">
                        <span class="text-[9px] bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-bold uppercase w-fit">
                          Hero #{{ mp.hero_id }}
                        </span>
                        <span class="text-[9px] bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded font-bold uppercase w-fit">
                          Role #{{ mp.role_id }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-if="schedule.matches.length === 0" class="p-10 text-center text-gray-400 italic text-sm">
                  Belum ada data match untuk jadwal ini.
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl">
              <h3 class="text-xs font-bold text-blue-400 uppercase tracking-tighter mb-4">Summary Stats</h3>
              <div class="space-y-4">
                <div class="flex justify-between items-end border-b border-white/10 pb-2">
                  <span class="text-xs text-gray-400">Total Match</span>
                  <span class="text-xl font-black">{{ schedule.matches.length }}</span>
                </div>
                <div class="flex justify-between items-end border-b border-white/10 pb-2">
                  <span class="text-xs text-gray-400">Avg. Player/Match</span>
                  <span class="text-xl font-black">{{ calculateAvgPlayers(schedule.matches) }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <h3 class="text-sm font-bold text-gray-900 mb-4 italic">Metadata</h3>
              <div class="space-y-3">
                <div class="text-[10px] flex flex-col">
                  <span class="text-gray-400 font-bold uppercase">Created At</span>
                  <span class="text-gray-700">{{ new Date(schedule.created_at).toLocaleString() }}</span>
                </div>
                <div class="text-[10px] flex flex-col">
                  <span class="text-gray-400 font-bold uppercase">Last Updated</span>
                  <span class="text-gray-700">{{ new Date(schedule.updated_at).toLocaleString() }}</span>
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
      schedule: null,
      loading: true,
    }
  },
  mounted() {
    this.fetchScheduleDetail();
  },
  methods: {
    async fetchScheduleDetail() {
      const id = this.$route.params.id;
      this.loading = true;
      try {
        const response = await axios.get(`/api/v1/schedules/${id}`);
        // Menyesuaikan struktur JSON Anda { data: { ... } }
        this.schedule = response.data.data;
      } catch (error) {
        console.error("Error loading detail:", error);
        alert("Gagal memuat data jadwal.");
      } finally {
        this.loading = false;
      }
    },
    formatFullDate(dateStr) {
      return new Date(dateStr).toLocaleString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }) + ' WIB';
    },
    statusBadge(status) {
      const base = "border font-bold ";
      switch(status) {
        case 'live': return base + "bg-red-50 text-red-600 border-red-100";
        case 'finished': return base + "bg-gray-50 text-gray-600 border-gray-100";
        case 'cancelled': return base + "bg-orange-50 text-orange-600 border-orange-100";
        default: return base + "bg-blue-50 text-blue-600 border-blue-100";
      }
    },
    calculateAvgPlayers(matches) {
      if (matches.length === 0) return 0;
      const total = matches.reduce((acc, m) => acc + m.players.length, 0);
      return (total / matches.length).toFixed(1);
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>