<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'matches'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95 cursor-pointer">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Input Hasil Match</h1>
            <p class="text-xs text-gray-500 font-medium italic">Catat detail pertandingan dan lineup hero</p>
          </div>
        </div>
      </header>

      <div class="p-6">
        <div class="max-w-7xl mx-auto">
          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-blue-500 pl-3">
                  Informasi Pertandingan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Season</label>
                    <select v-model="form.season_id"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm font-medium"
                      required>
                      <option v-for="s in seasons" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Jadwal Stream</label>

                    <div v-if="form.schedule_id"
                      class="px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold text-gray-700 flex justify-between items-center">
                      {{ selectedScheduleName }}
                      <i class="fas fa-lock text-gray-300 text-xs"></i>
                    </div>

                    <select v-else v-model="form.schedule_id"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm font-medium"
                      required>
                      <option value="" disabled selected>Pilih Jadwal...</option>
                      <option v-for="sch in schedules" :key="sch.id" :value="sch.id">{{ sch.title }}</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                  <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest italic border-l-4 border-orange-500 pl-3">
                    Lineup Pemain
                  </h2>
                  <button type="button" @click="addPlayer"
                    class="text-[10px] font-black text-blue-600 hover:bg-blue-50 px-3 py-1 rounded-lg transition-all cursor-pointer flex items-center gap-1 active:scale-95">
                    <i class="fas fa-plus mr-1"></i> TAMBAH PEMAIN
                  </button>
                </div>

                <div class="space-y-4">
                  <div v-for="(player, index) in form.players" :key="index"
                    class="relative p-4 rounded-xl border border-gray-100 bg-gray-50/50 grid grid-cols-1 md:grid-cols-3 gap-3 items-end group transition-all duration-300"
                    :class="{'ring-2 ring-blue-400 bg-blue-50/20': player.player_id}">

                    <button v-if="form.players.length > 1" type="button" @click="removePlayer(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-white border border-red-100 text-red-500 rounded-full shadow-sm flex items-center justify-center hover:bg-red-500 hover:text-white transition-all z-10 cursor-pointer">
                      <i class="fas fa-times text-[10px]"></i>
                    </button>

                    <div class="relative">
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Player {{ index + 1 }}</label>
                      <input type="text" v-model="player.player_search_text" @focus="player.player_show_dropdown = true"
                        @blur="closeDropdownLater(player, 'player')" @input="handlePlayerInput(player)"
                        placeholder="Cari pemain..."
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500"
                        required />

                      <ul v-if="player.player_show_dropdown && filteredPlayers(player.player_search_text, index).length"
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto custom-scrollbar">
                        <li v-for="p in filteredPlayers(player.player_search_text, index)" :key="p.id"
                          @mousedown="selectPlayer(player, p)"
                          class="px-3 py-2 text-xs font-bold text-gray-700 cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition-colors">
                          {{ p.name }} ({{ p.mlbb_id }}) <span class="text-gray-400 font-normal">({{ p.type }})</span>
                        </li>
                      </ul>
                    </div>

                    <div class="relative">
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Hero</label>
                      <input type="text" v-model="player.hero_search_text" @focus="player.hero_show_dropdown = true"
                        @blur="closeDropdownLater(player, 'hero')" @input="handleHeroInput(player)"
                        placeholder="Cari hero..."
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500"
                        required />

                      <ul v-if="player.hero_show_dropdown && filteredHeroes(player.hero_search_text, index).length"
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto custom-scrollbar">
                        <li v-for="h in filteredHeroes(player.hero_search_text, index)" :key="h.id"
                          @mousedown="selectHero(player, h)"
                          class="px-3 py-2 text-xs font-bold text-gray-700 cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition-colors">
                          {{ h.name }}
                        </li>
                      </ul>
                    </div>

                    <div>
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Role</label>
                      <select v-model="player.role_id"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500"
                        required>
                        <option value="" disabled selected>Pilih Role...</option>
                        <option v-for="r in getAvailableRoles(index)" :key="r.id" :value="r.id">
                          {{ r.name }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-6">
              
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 text-center">Finalisasi</h3>

                <div class="space-y-3 mb-6">
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-500 font-medium">Total Pemain</span>
                    <span class="text-gray-900 font-bold">{{ form.players.length }} Terdaftar</span>
                  </div>
                </div>

                <button type="submit" :disabled="submitting || !isFormValid"
                  class="cursor-pointer w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                  <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
                  <span>{{ submitting ? 'MEMPROSES...' : 'POST MATCH DATA' }}</span>
                </button>

                <button type="button" @click="$router.back()"
                  class="w-full mt-3 py-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                  BATAL & KEMBALI
                </button>
              </div>

              <div class="bg-orange-50 border border-orange-100 p-4 rounded-2xl">
                <div class="flex gap-3">
                  <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5 text-sm"></i>
                  <p class="text-[10px] text-orange-700 leading-relaxed font-medium">
                    Sistem otomatis memotong balance pemain berstatus <b>VIP</b> sebanyak 1 point tiap match.
                  </p>
                </div>
              </div>

              <div v-if="form.schedule_id" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-list-ol text-blue-500"></i> Live Queue
                  </h3>
                  <button type="button" @click="fetchQueues" class="text-gray-400 hover:text-blue-500 transition-colors cursor-pointer">
                    <i class="fas fa-sync-alt text-[10px]" :class="{'animate-spin': queueLoading}"></i>
                  </button>
                </div>

                <div class="mb-4">
                  <h4 class="text-[9px] font-black text-amber-500 uppercase mb-2 border-b border-gray-100 pb-1">VIP</h4>
                  <ul class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                    <li v-for="q in vipQueues" :key="q.id" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                      <div>
                        <p class="text-[10px] font-bold text-gray-800 uppercase">{{ q.name }}</p>
                        <p class="text-[8px] text-gray-400 font-medium">Balance: {{ q.wallet?.play_balance || 0 }}</p>
                      </div>
                      
                      <div class="flex flex-col items-end gap-1">
                        <span :class="statusStyle(q.status)" class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider">
                          {{ q.status }}
                        </span>
                        <div v-if="!['playing', 'done', 'skipped'].includes(q.status)" class="flex gap-1 mt-1">
                          <button @click="acceptQueue(q)" type="button" class="w-6 h-6 bg-blue-100 hover:bg-blue-500 text-blue-600 hover:text-white rounded flex items-center justify-center transition-colors cursor-pointer" title="Terima">
                            <i class="fas fa-check text-[10px]"></i>
                          </button>
                          <button @click="rejectQueue(q)" type="button" class="w-6 h-6 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded flex items-center justify-center transition-colors cursor-pointer" title="Tolak">
                            <i class="fas fa-times text-[10px]"></i>
                          </button>
                        </div>
                      </div>
                    </li>
                    <li v-if="vipQueues.length === 0" class="text-[10px] text-gray-400 italic py-2 text-center">Kosong</li>
                  </ul>
                </div>

                <div>
                  <h4 class="text-[9px] font-black text-indigo-500 uppercase mb-2 border-b border-gray-100 pb-1">Public Queue</h4>
                  <ul class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar pr-1">
                    <li v-for="q in publicQueues" :key="q.id" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                      <div>
                        <p class="text-[10px] font-bold text-gray-800 uppercase">#{{ q.queue_number }} - {{ q.nickname || q.name }}</p>
                        <p class="text-[8px] text-gray-400 font-medium">{{ q.mlbb_id }} - {{ q.mlbb_server }}</p>
                      </div>
                      
                      <div class="flex flex-col items-end gap-1">
                        <span :class="statusStyle(q.status)" class="px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-wider">
                          {{ q.status }}
                        </span>
                        <div v-if="!['playing', 'done', 'skipped'].includes(q.status)" class="flex gap-1 mt-1">
                          <button @click="acceptQueue(q)" type="button" class="w-6 h-6 bg-blue-100 hover:bg-blue-500 text-blue-600 hover:text-white rounded flex items-center justify-center transition-colors cursor-pointer" title="Terima">
                            <i class="fas fa-check text-[10px]"></i>
                          </button>
                          <button @click="rejectQueue(q)" type="button" class="w-6 h-6 bg-red-100 hover:bg-red-500 text-red-600 hover:text-white rounded flex items-center justify-center transition-colors cursor-pointer" title="Tolak">
                            <i class="fas fa-times text-[10px]"></i>
                          </button>
                        </div>
                      </div>
                    </li>
                    <li v-if="publicQueues.length === 0" class="text-[10px] text-gray-400 italic py-2 text-center">Kosong</li>
                  </ul>
                </div>

              </div>

            </div>

          </form>
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
      submitting: false,
      seasons: [],
      schedules: [],
      playersList: [],
      heroes: [],
      roles: [],
      form: {
        season_id: '',
        schedule_id: '',
        players: [
          {
            player_id: '', player_search_text: '', player_show_dropdown: false,
            hero_id: '', hero_search_text: '', hero_show_dropdown: false,
            role_id: ''
          }
        ]
      },
      queueLoading: false,
      vipQueues: [],
      publicQueues: [],
      queueTimer: null
    }
  },
  computed: {
    isFormValid() {
      const allFilled = this.form.players.every(p =>
        p.player_id !== '' && p.hero_id !== '' && p.role_id !== '' && this.form.schedule_id && this.form.season_id
      );
      if (!allFilled) return false;

      const uniquePlayers = new Set(this.form.players.map(p => p.player_id));
      const uniqueHeroes = new Set(this.form.players.map(p => p.hero_id));
      const uniqueRoles = new Set(this.form.players.map(p => p.role_id));
      return uniquePlayers.size === this.form.players.length &&
        uniqueHeroes.size === this.form.players.length &&
        uniqueRoles.size === this.form.players.length;
    },
    selectedScheduleName() {
      if (!this.form.schedule_id || this.schedules.length === 0) return 'Memuat...';
      const found = this.schedules.find(s => s.id == this.form.schedule_id);
      return found ? found.title : 'Jadwal tidak ditemukan';
    },
  },
  watch: {
    'form.schedule_id'(newVal) {
      if (newVal) {
        this.fetchQueues();
      }
    }
  },
  async mounted() {
    await this.fetchInitialData();

    const urlId = this.$route.params.id;

    if (urlId) {
      this.form.schedule_id = parseInt(urlId);

      const currentSchedule = this.schedules.find(s => s.id == urlId);
      if (currentSchedule && currentSchedule.season_id) {
        this.form.season_id = currentSchedule.season_id;
      }
    }

    this.queueTimer = setInterval(() => {
      if (this.form.schedule_id) this.fetchQueues();
    }, 30000);
  },
  beforeUnmount() {
    if (this.queueTimer) clearInterval(this.queueTimer);
  },
  methods: {
    // --- AKSI TERIMA & TOLAK ANTARAAN ---
    acceptQueue(q) {
      // 1. Ambil data ID dan Nama dari objek antrean
      // Asumsi API antrean memiliki relasi player_id, jika tidak ada fallback ke q.id
      const playerId = q.player_id || q.id; 
      const playerName = q.name || q.nickname;

      // Cek apakah pemain ini sudah ada di dalam form lineup
      const alreadyInForm = this.form.players.some(p => p.player_id === playerId);
      if (alreadyInForm) {
        alert("Pemain ini sudah ada di dalam form lineup!");
        return;
      }

      // 2. Cari baris di form.players yang player_id-nya masih kosong
      let emptySlot = this.form.players.find(p => p.player_id === '');

      // 3. Jika tidak ada yang kosong, tambahkan baris baru secara otomatis (maksimal batas bebas, MLBB biasa 5)
      if (!emptySlot) {
        this.addPlayer();
        emptySlot = this.form.players[this.form.players.length - 1];
      }

      // 4. Masukkan data ke slot tersebut
      emptySlot.player_id = playerId;
      emptySlot.player_search_text = playerName;

      // 5. Ubah status antrean menjadi 'playing' secara instan
      this.updateQueueStatus(q.id, 'playing');
    },

    rejectQueue(q) {
      const playerName = q.name || q.nickname;
      if (confirm(`Apakah kamu yakin ingin me-skip / menolak ${playerName}?`)) {
        this.updateQueueStatus(q.id, 'skipped');
      }
    },

    async updateQueueStatus(queueId, status) {
      // Jika kamu punya API untuk update status Queue, panggil di sini
      try {
        // Asumsi Endpoint-nya seperti ini. Jika beda, silakan sesuaikan dengan route Backend-mu.
        await axios.put(`/api/v1/queues/${queueId}/status`, { status });
        this.fetchQueues(); // Refresh UI Queue
      } catch (e) {
        console.error("Gagal mengubah status antrean:", e);
      }
    },

    // --- METHOD QUEUE BAWAAN ---
    async fetchQueues() {
      if (!this.form.schedule_id) return;
      this.queueLoading = true;
      try {
        const res = await axios.get(`/api/v1/queues/${this.form.schedule_id}`);
        if (res.data.status) {
          this.vipQueues = res.data.data.vip_queues;
          this.publicQueues = res.data.data.public_queues;
        }
      } catch (e) {
        console.error("Gagal load data antrean:", e);
      } finally {
        this.queueLoading = false;
      }
    },
    statusStyle(status) {
      switch (status) {
        case 'playing': return 'bg-emerald-500 text-white border border-emerald-600';
        case 'done': return 'bg-gray-200 text-gray-500 border border-gray-300';
        case 'skipped': return 'bg-red-100 text-red-600 border border-red-200';
        default: return 'bg-blue-100 text-blue-700 border border-blue-200';
      }
    },

    // --- METHOD FORM MATCH BAWAAN ---
    getAvailableRoles(currentIndex) {
      const selectedRoleIds = this.form.players
        .filter((_, index) => index !== currentIndex)
        .map(p => p.role_id)
        .filter(id => id !== '');

      return this.roles.filter(r => {
        return !selectedRoleIds.includes(r.id) || r.id === this.form.players[currentIndex].role_id;
      });
    },
    async fetchInitialData() {
      try {
        const [s, sch, p, h, r] = await Promise.all([
          axios.get('/api/v1/seasons'),
          axios.get('/api/v1/schedules'),
          axios.get('/api/v1/players'),
          axios.get('/api/v1/heroes'),
          axios.get('/api/v1/roles')
        ]);
        this.seasons = s.data.data || s.data;
        this.schedules = sch.data.data?.data || sch.data.data || sch.data;
        this.playersList = p.data.data || p.data;
        this.heroes = h.data.data || h.data;
        this.roles = r.data.data || r.data;
      } catch (err) {
        console.error("Gagal memuat data referensi", err);
      }
    },

    addPlayer() {
      this.form.players.push({
        player_id: '', player_search_text: '', player_show_dropdown: false,
        hero_id: '', hero_search_text: '', hero_show_dropdown: false,
        role_id: ''
      });
    },

    removePlayer(index) {
      this.form.players.splice(index, 1);
    },

    closeDropdownLater(player, type) {
      setTimeout(() => {
        if (type === 'player') player.player_show_dropdown = false;
        if (type === 'hero') player.hero_show_dropdown = false;
      }, 200);
    },

    filteredPlayers(query, currentIndex) {
      const selectedIds = this.form.players
        .map((p, i) => (i !== currentIndex ? p.player_id : null))
        .filter(id => id);
      let available = this.playersList.filter(p => !selectedIds.includes(p.id));
      if (!query) return available;
      return available.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));
    },

    selectPlayer(slot, p) {
      slot.player_id = p.id;
      slot.player_search_text = p.name;
      slot.player_show_dropdown = false;
    },

    handlePlayerInput(slot) {
      slot.player_id = '';
      slot.player_show_dropdown = true;
    },

    filteredHeroes(query, currentIndex) {
      const selectedIds = this.form.players
        .map((p, i) => (i !== currentIndex ? p.hero_id : null))
        .filter(id => id);
      let available = this.heroes.filter(h => !selectedIds.includes(h.id));
      if (!query) return available;
      return available.filter(h => h.name.toLowerCase().includes(query.toLowerCase()));
    },

    selectHero(slot, h) {
      slot.hero_id = h.id;
      slot.hero_search_text = h.name;
      slot.hero_show_dropdown = false;
    },

    handleHeroInput(slot) {
      slot.hero_id = '';
      slot.hero_show_dropdown = true;
    },

    async handleSubmit() {
      this.submitting = true;
      try {
        const payload = {
          season_id: this.form.season_id,
          schedule_id: this.form.schedule_id,
          players: this.form.players.map(p => ({
            player_id: p.player_id,
            hero_id: p.hero_id,
            role_id: p.role_id
          }))
        };
        await axios.post('/api/v1/matches', payload);
        this.$router.push(`/schedules/${this.form.schedule_id}`);
      } catch (error) {
        alert("Gagal: " + (error.response?.data?.message || "Terjadi kesalahan"));
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>