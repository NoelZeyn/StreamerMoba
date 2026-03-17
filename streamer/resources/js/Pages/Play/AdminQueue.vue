<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden font-sans text-slate-900">
    <Sidebar :activeMenu="'queues'" />

    <main class="flex-1 min-w-0 flex flex-col overflow-hidden">
      <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between z-20 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-indigo-100 shadow-lg">
            <i class="fas fa-layer-group text-white text-lg"></i>
          </div>
          <div>
            <h1 class="text-lg font-bold text-slate-800 leading-tight">Antrean Live Stream</h1>
            <div class="flex items-center gap-2 mt-0.5">
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase tracking-wider border border-slate-200">
                SID: {{ scheduleId }}
              </span>
              <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
              <p class="text-[11px] text-slate-500 font-medium italic">Otomatis sinkron setiap 30 detik</p>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button @click="refreshAll" 
            class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all active:scale-95 shadow-sm">
            <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
            Refresh
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-7xl mx-auto space-y-8">
          
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <section class="flex flex-col space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-6 bg-amber-400 rounded-full"></div>
                  <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Prioritas VIP</h3>
                </div>
                
                <div class="relative group">


                  <Transition name="fade">
                    <div v-if="vipResults.length > 0"
                      class="absolute z-30 top-full right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden ring-1 ring-black ring-opacity-5">
                      <div v-for="player in vipResults" :key="player.id" @click="addVipDirectly(player)"
                        class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center border-b border-slate-50 last:border-0 group/item transition-colors">
                        <div>
                          <p class="text-xs font-bold text-slate-800 uppercase">{{ player.name }}</p>
                          <p class="text-[10px] text-slate-500 font-mono italic">ID: {{ player.mlbb_id || '-' }}</p>
                        </div>
                        <i class="fas fa-plus-circle text-slate-300 group-hover/item:text-indigo-500 transition-colors"></i>
                      </div>
                    </div>
                  </Transition>
                </div>
              </div>

              <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full">
                  <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                      <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Player</th>
                      <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                      <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest pr-10">Kontrol</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr v-for="q in vipQueues" :key="q.id" 
                      class="group transition-colors" :class="q.status === 'playing' ? 'bg-amber-50/30' : 'hover:bg-slate-50/50'">
                      <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                          <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 font-black text-[10px]">VIP</div>
                          <div>
                            <p class="text-xs font-bold text-slate-800 uppercase">{{ q.name }}</p>
                            <p v-if="q.wallet" class="text-[10px] text-slate-500 font-medium">Balance: <span class="text-emerald-600 font-bold font-mono">{{ q.wallet.play_balance }}</span></p>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <span :class="statusStyle(q.status)" class="px-3 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter">
                          {{ q.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex justify-end gap-1.5">
                          <button v-if="q.status === 'pending'" @click="updateStatus(q.id, 'playing')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-play text-[10px]"></i>
                          </button>
                          <button v-if="q.status === 'playing'" @click="updateStatus(q.id, 'completed')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-check text-[10px]"></i>
                          </button>
                          <button @click="updateStatus(q.id, 'skipped')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-forward text-[10px]"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="vipQueues.length === 0" class="py-12 text-center text-slate-400 text-xs italic">Antrean VIP kosong.</div>
              </div>
            </section>

            <section class="flex flex-col space-y-4">
              <div class="flex items-center gap-2">
                <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Antrean Publik</h3>
                <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full font-bold ml-2">{{ publicQueues.length }} Player</span>
              </div>

              <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex-1">
                <table class="w-full">
                  <thead class="bg-slate-50/80 border-b border-slate-100">
                    <tr>
                      <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest w-16">Pos</th>
                      <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Player Info</th>
                      <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                      <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest pr-10">Kontrol</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr v-for="q in publicQueues" :key="q.id" 
                      class="group transition-colors" :class="q.status === 'playing' ? 'bg-indigo-50/30' : 'hover:bg-slate-50/50'">
                      <td class="px-6 py-4">
                        <span class="text-xs font-mono font-bold text-slate-400">#{{ String(q.queue_number).padStart(2, '0') }}</span>
                      </td>
                      <td class="px-6 py-4">
                        <p class="text-xs font-bold text-slate-800 uppercase">{{ q.nickname }}</p>
                        <p class="text-[10px] text-slate-400 font-medium">MLBB Server: <span class="text-slate-600 font-bold font-mono">{{ q.mlbb_server }}</span></p>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <span :class="statusStyle(q.status)" class="px-3 py-1 rounded-md text-[9px] font-black uppercase tracking-tighter">
                          {{ q.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex justify-end gap-1.5">
                          <button v-if="q.status === 'pending'" @click="updateStatus(q.id, 'playing')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-play text-[10px]"></i>
                          </button>
                          <button v-if="q.status === 'playing'" @click="updateStatus(q.id, 'completed')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-check text-[10px]"></i>
                          </button>
                          <button @click="updateStatus(q.id, 'skipped')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-forward text-[10px]"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="publicQueues.length === 0" class="py-12 text-center text-slate-400 text-xs italic">Antrean publik kosong.</div>
              </div>
            </section>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

<script>
import Sidebar from '@/components/Sidebar.vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

export default {
  components: { Sidebar },
  data() {
    return {
      loading: false,
      submitting: false,
      vipResults: [],
      vipQueues: [],
      publicQueues: [],
      scheduleId: this.$route.params.id
    }
  },
  mounted() {
    this.fetchQueues();
    this.timer = setInterval(this.fetchQueues, 30000);
  },
  beforeUnmount() {
    clearInterval(this.timer);
  },
  methods: {
    async fetchQueues() {
      if (!this.scheduleId) return;
      this.loading = true;
      try {
        const res = await axios.get(`/api/v1/queues/${this.scheduleId}`);
        if (res.data.status) {
          this.vipQueues = res.data.data.vip_queues;
          this.publicQueues = res.data.data.public_queues;
        }
      } catch (e) {
        console.error("Gagal load data");
      } finally {
        this.loading = false;
      }
    },

    async addVipDirectly(player) {
      if (this.submitting) return;
      this.submitting = true;
      try {
        await axios.post('/api/v1/public/join-queue', {
          schedule_id: this.scheduleId,
          user_id: player.id,
          nickname: player.name,
          mlbb_id: player.mlbb_id,
          is_vip: true
        });

        this.searchVipQuery = '';
        this.vipResults = [];
        this.fetchQueues();
      } catch (e) {
        alert(e.response?.data?.message || "Gagal menambah VIP");
      } finally {
        this.submitting = false;
      }
    },

    async updateStatus(queueId, status) {
      try {
        await axios.patch(`/api/v1/queues/${queueId}/status`, { status });
        this.fetchQueues();
      } catch (e) {
        alert("Gagal memperbarui status");
      }
    },

    refreshAll() {
      this.fetchQueues();
    },

    statusStyle(status) {
      switch (status) {
        case 'playing': return 'bg-emerald-500 text-white shadow-sm';
        case 'completed': return 'bg-gray-100 text-gray-400 border border-gray-200';
        case 'skipped': return 'bg-red-50 text-red-500 border border-red-100';
        default: return 'bg-blue-50 text-blue-600 border border-blue-100';
      }
    }
  }
}
</script>