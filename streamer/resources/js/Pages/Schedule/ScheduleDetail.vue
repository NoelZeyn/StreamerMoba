<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'schedules'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">{{ schedule?.title || 'Loading...' }}</h1>
            <p class="text-xs text-gray-500 font-medium">Detail Jadwal Streaming</p>
          </div>
        </div>
        <div v-if="schedule" :class="statusBadge(schedule.status)"
          class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
          {{ schedule.status }}
        </div>
      </header>

      <div class="p-6">
        <div v-if="loading" class="flex items-center justify-center min-h-[400px]">
          <div class="animate-spin w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full"></div>
        </div>

        <div v-else-if="schedule" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
              <h2 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest italic">Streaming Info</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                  <p class="text-[10px] font-bold text-gray-400 uppercase">Waktu Mulai</p>
                  <p class="text-sm font-bold text-gray-800">
                    {{ formatFullDate(schedule.start_time) }}
                  </p>
                </div>

                <!-- Waktu Selesai hanya jika finished -->
                <div v-if="schedule.status === 'finished'"
                  class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                  <p class="text-[10px] font-bold text-emerald-500 uppercase">Waktu Selesai</p>
                  <p class="text-sm font-bold text-emerald-700">
                    {{ formatFullDate(schedule.end_time) }}
                  </p>
                </div>

                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                  <p class="text-[10px] font-bold text-gray-400 uppercase">Streamer</p>
                  <p class="text-sm font-bold text-gray-800 italic">
                    Streamer {{ schedule.matches[0]?.user?.name }}
                  </p>
                </div>
              </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
              <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h2 class="text-sm font-bold text-gray-900">Riwayat Match</h2>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase text-center w-12">No</th>
                      <!-- <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Detail Match</th> -->
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Pemain & Hero</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Tanggal</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="(match, index) in paginatedMatches" :key="match.id"
                      class="hover:bg-gray-50/50 transition-colors">
                      <td class="px-4 py-6 text-xs text-center font-medium text-gray-700">
                        {{ ((currentPage - 1) * itemsPerPage) + index + 1 }}
                      </td>
                      <!-- <td class="px-4 py-6">
                        <div class="flex flex-col gap-1">
                          <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded w-fit">ID #{{
                            match.id }}</span>
                        </div>
                      </td> -->
                      <td class="px-4 py-6">
                        <div class="grid grid-cols-5 gap-2">
                          <div v-for="mp in match.players" :key="mp.id"
                            class="flex flex-col border border-gray-100 rounded p-2 bg-white shadow-sm text-center">
                            <span class="text-[10px] font-bold text-gray-800 truncate">
                              {{ mp.player?.name || 'Unknown' }}
                            </span>

                            <div class="flex justify-center gap-1 mt-1">
                              <span class="text-[8px] bg-purple-50 text-purple-600 px-1 rounded uppercase font-bold">
                                {{ mp.hero?.name || 'Hero' }}
                              </span>

                              <span class="text-[8px] bg-orange-50 text-orange-600 px-1 rounded uppercase font-bold">
                                {{ mp.role?.name || 'Role' }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-6 text-[11px] text-gray-600 font-medium">
                        {{ match.played_at }}
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div v-if="schedule.matches.length === 0" class="p-16 text-center bg-white">
                  <p class="text-gray-400 italic text-sm">Belum ada data match untuk jadwal ini.</p>
                </div>
              </div>

              <div class="p-4 bg-white border-t border-gray-100 flex items-center justify-between">
                <button @click="currentPage--" :disabled="currentPage === 1"
                  class="px-4 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded disabled:opacity-50 hover:bg-gray-200 transition-all">
                  Prev
                </button>

                <span class="text-xs font-medium text-gray-700">
                  Halaman {{ currentPage }} dari {{ totalPages }}
                </span>

                <button @click="currentPage++" :disabled="currentPage === totalPages"
                  class="px-4 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded disabled:opacity-50 hover:bg-gray-200 transition-all">
                  Next
                </button>
              </div>
            </div>
          </div>

          <div>

            <div class="space-y-6">
              <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden">
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6">Summary Stats</h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-end border-b border-white/10 pb-2">
                    <span class="text-xs text-gray-400">Total Match</span>
                    <span class="text-2xl font-black">{{ schedule.matches.length }}</span>
                  </div>
                  <div class="flex justify-between items-end border-b border-white/10 pb-2">
                    <span class="text-xs text-gray-400">Avg. Player</span>
                    <span class="text-2xl font-black text-blue-400">{{ calculateAvgPlayers(schedule.matches) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-10 mt-10">
              <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden">
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6">Shortcuts</h3>
                <div class="grid grid-cols-2 gap-3">

                  <!-- NEW MATCH -->
                  <router-link :to="{
                    path: '/matches/create',
                    query: {
                      schedule_id: schedule.id,
                      season_id: schedule.season_id,
                      title: schedule.title
                    }
                  }" class="bg-white/5 hover:bg-white/10 p-4 rounded-xl text-center border border-white/10 transition-all group">
                    <i class="fas fa-plus-circle mb-2 block text-blue-400 group-hover:scale-110 transition"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-blue-300">
                      New Match
                    </p>
                  </router-link>

                  <button v-if="schedule.status === 'scheduled'" @click="startStream"
                    class="bg-white/5 hover:bg-emerald-500/20 p-4 rounded-xl text-center border border-white/10 transition-all active:scale-95 group cursor-pointer">
                    <i class="fas fa-play-circle mb-2 block text-emerald-400 group-hover:scale-110 transition"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-300">
                      Start
                    </p>
                  </button>

                  <!-- FINISH STREAM -->
                  <button v-if="schedule.status === 'live'" @click="finishStream"
                    class="bg-white/5 hover:bg-purple-500/20 p-4 rounded-xl text-center border border-white/10 transition-all active:scale-95 group cursor-pointer">
                    <i class="fas fa-flag-checkered mb-2 block text-purple-400 group-hover:scale-110 transition"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-purple-300">
                      Finish
                    </p>
                  </button>

                  <!-- CANCEL STREAM -->
                  <button v-if="schedule.status === 'scheduled'" @click="cancelStream"
                    class="bg-white/5 hover:bg-red-500/20 p-4 rounded-xl text-center border border-white/10 transition-all active:scale-95 group cursor-pointer">
                    <i class="fas fa-times-circle mb-2 block text-red-400 group-hover:scale-110 transition"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-red-300">
                      Cancel
                    </p>
                  </button>

                  <!-- REOPEN STREAM -->
                  <button v-if="schedule.status === 'cancelled'" @click="reopenStream"
                    class="bg-white/5 hover:bg-yellow-500/20 p-4 rounded-xl text-center border border-white/10 transition-all active:scale-95 group cursor-pointer">
                    <i class="fas fa-undo mb-2 block text-yellow-400 group-hover:scale-110 transition"></i>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-yellow-300">
                      Reopen
                    </p>
                  </button>
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
      currentPage: 1,
      itemsPerPage: 10
    }
  },
  computed: {
    totalPages() {
      if (!this.schedule || !this.schedule.matches) return 0;
      return Math.ceil(this.schedule.matches.length / this.itemsPerPage);
    },
    paginatedMatches() {
      if (!this.schedule || !this.schedule.matches) return [];
      const matches = [...this.schedule.matches];
      matches.sort((a, b) => {
        return new Date(b.played_at) - new Date(a.played_at);
      });
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;

      return matches.slice(start, end);
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
        this.schedule = response.data.data;
      } catch (error) {
        console.error("Error loading detail:", error);
      } finally {
        this.loading = false;
      }
    },
    async finishStream() {
      if (!this.schedule) return;

      try {
        await axios.post(`/api/v1/schedules/${this.schedule.id}/finish`);
        this.fetchScheduleDetail();
      } catch (error) {
        console.error("Error finishing stream:", error);
      }
    },

    async reopenStream() {
      if (!this.schedule) return;

      try {
        await axios.post(`/api/v1/schedules/${this.schedule.id}/reopen`);
        this.fetchScheduleDetail();
      } catch (error) {
        console.error("Error reopening stream:", error);
      }
    },

    async startStream() {
      if (!this.schedule) return;

      try {
        await axios.post(`/api/v1/schedules/${this.schedule.id}/start`);
        this.fetchScheduleDetail();
      } catch (error) {
        console.error("Error starting stream:", error);
      }
    },

    async cancelStream() {
      if (!this.schedule) return;

      try {
        await axios.post(`/api/v1/schedules/${this.schedule.id}/cancel`);
        this.fetchScheduleDetail();
      } catch (error) {
        console.error("Error cancelling stream:", error);
      }
    },

    formatFullDate(dateStr) {
      if (!dateStr) return '-';
      return new Date(dateStr).toLocaleString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }) + ' WIB';
    },
    formatDateSimple(dateStr) {
      if (!dateStr) return '-';
      return new Date(dateStr).toLocaleDateString('id-ID');
    },
    statusBadge(status) {
      const base = "border font-bold ";
      switch (status) {
        case 'live': return base + "bg-red-50 text-red-600 border-red-100";
        case 'finished': return base + "bg-emerald-50 text-emerald-600 border-emerald-100";
        case 'cancelled': return base + "bg-gray-50 text-gray-600 border-gray-100";
        default: return base + "bg-blue-50 text-blue-600 border-blue-100";
      }
    },
    calculateAvgPlayers(matches) {
      if (!matches || matches.length === 0) return 0;
      const total = matches.reduce((acc, m) => acc + (m.players?.length || 0), 0);
      return (total / matches.length).toFixed(1);
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

table {
  min-width: 700px;
}
</style>