<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans text-slate-900">
    <Sidebar :activeMenu="'queues'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
            <i class="fas fa-list-ol text-white text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Queue Management</h1>
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest">SID: {{ scheduleId }} • Atur Antrean</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button @click="refreshAll" class="p-2.5 hover:bg-gray-100 rounded-xl transition-all border border-gray-200">
            <i class="fas fa-sync-alt text-gray-500" :class="{ 'animate-spin': loading }"></i>
          </button>
        </div>
      </header>

      <div class="p-8 max-w-7xl mx-auto w-full space-y-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-black uppercase tracking-widest text-amber-600 flex items-center gap-2">
                <i class="fas fa-crown"></i> VIP List
              </h3>
              <div class="relative w-64">
                <input v-model="searchVipQuery" @input="searchVipPlayers" type="text"
                  placeholder="Cari & Tambah VIP..."
                  class="w-full pl-4 pr-10 py-2 text-xs rounded-xl bg-white border border-gray-200 focus:border-amber-500 outline-none transition-all shadow-sm" />
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>

                <div v-if="vipResults.length > 0"
                  class="absolute z-30 top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 border-t-4 border-t-amber-500">
                  <div v-for="player in vipResults" :key="player.id" @click="addVipDirectly(player)"
                    class="p-3 hover:bg-amber-50 cursor-pointer flex justify-between items-center border-b border-gray-50 last:border-0 transition-colors">
                    <div>
                      <p class="text-[10px] font-black text-gray-900 uppercase">{{ player.name }}</p>
                      <p class="text-[9px] text-gray-500">ID: {{ player.mlbb_id || '-' }}</p>
                    </div>
                    <i class="fas fa-plus-circle text-amber-500"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
              <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                  <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Player</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="q in vipQueues" :key="q.id" :class="{ 'bg-amber-50/50': q.status === 'playing' }">
                    <td class="px-6 py-5">
                      <p class="text-sm font-black text-gray-900 uppercase">{{ q.name }}</p>
                      <p v-if="q.wallet" class="text-[10px] text-amber-600 font-bold">
                        Credit: {{ q.wallet.play_balance }}
                      </p>
                    </td>
                    <td class="px-6 py-5">
                      <span :class="statusStyle(q.status)"
                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase">
                        {{ q.status }}
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <div class="flex justify-center gap-2">
                        <button v-if="q.status === 'pending'" @click="updateStatus(q.id, 'playing')"
                          class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all">
                          <i class="fas fa-play text-xs"></i>
                        </button>
                        <button v-if="q.status === 'playing'" @click="updateStatus(q.id, 'done')"
                          class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                          <i class="fas fa-check text-xs"></i>
                        </button>
                        <button @click="updateStatus(q.id, 'skipped')"
                          class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                          <i class="fas fa-forward text-xs"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="vipQueues.length === 0" class="p-10 text-center text-gray-400 text-xs italic font-medium">Belum ada VIP di antrean.</div>
            </div>
          </div>

          <div class="space-y-4">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600 flex items-center gap-2">
              <i class="fas fa-users"></i> Public Queue
            </h3>
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
              <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                  <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">No</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Player</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-for="q in publicQueues" :key="q.id" :class="{ 'bg-indigo-50/50': q.status === 'playing' }">
                    <td class="px-6 py-5 text-sm font-bold text-gray-400">#{{ q.queue_number }}</td>
                    <td class="px-6 py-5">
                      <p class="text-sm font-black text-gray-900 uppercase">{{ q.nickname }}</p>
                      <p class="text-[10px] text-gray-400 font-bold">Server: {{ q.mlbb_server }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <span :class="statusStyle(q.status)"
                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase">
                        {{ q.status }}
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <div class="flex justify-center gap-2">
                        <button v-if="q.status === 'pending'" @click="updateStatus(q.id, 'playing')"
                          class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all">
                          <i class="fas fa-play text-xs"></i>
                        </button>
                        <button v-if="q.status === 'playing'" @click="updateStatus(q.id, 'done')"
                          class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                          <i class="fas fa-check text-xs"></i>
                        </button>
                        <button @click="updateStatus(q.id, 'skipped')"
                          class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                          <i class="fas fa-forward text-xs"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="publicQueues.length === 0" class="p-10 text-center text-gray-400 text-xs italic font-medium">Belum ada player di antrean.</div>
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
import debounce from 'lodash/debounce';

export default {
  components: { Sidebar },
  data() {
    return {
      loading: false,
      submitting: false,
      searchVipQuery: '',
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

    searchVipPlayers: debounce(async function () {
      if (this.searchVipQuery.length < 2) {
        this.vipResults = [];
        return;
      }
      try {
        const res = await axios.get('/api/v1/vip/search', {
          params: { name: this.searchVipQuery }
        });
        this.vipResults = res.data.data;
      } catch (e) {
        console.error("Pencarian VIP gagal");
      }
    }, 500),

    // Fungsi baru: Pilih langsung dari dropdown dan tambah ke queue
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
        case 'done': return 'bg-gray-100 text-gray-400 border border-gray-200';
        case 'skipped': return 'bg-red-50 text-red-500 border border-red-100';
        default: return 'bg-blue-50 text-blue-600 border border-blue-100';
      }
    }
  }
}
</script>