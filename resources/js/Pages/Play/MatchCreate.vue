<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'matches'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()"
            class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95 cursor-pointer">
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
                <h2
                  class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-blue-500 pl-3">
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
                  <h2
                    class="text-sm font-bold text-gray-900 uppercase tracking-widest italic border-l-4 border-orange-500 pl-3">
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
                    :class="{ 'ring-2 ring-blue-400 bg-blue-50/20': player.player_id }">

                    <button v-if="form.players.length > 1" type="button" @click="removePlayer(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-white border border-red-100 text-red-500 rounded-full shadow-sm flex items-center justify-center hover:bg-red-500 hover:text-white transition-all z-10 cursor-pointer">
                      <i class="fas fa-times text-[10px]"></i>
                    </button>

                    <div class="relative">
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Player {{ index + 1
                      }}</label>
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
                        <option v-for="r in getAvailableRoles(index)" :key="r.id" :value="r.id">{{ r.name }}</option>
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
                  class="cursor-pointer w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50">
                  <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
                  <span>{{ submitting ? 'MEMPROSES...' : 'POST MATCH DATA' }}</span>
                </button>
              </div>

              <div v-if="form.schedule_id"
                class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-stream text-blue-500"></i> Queue
                  </h3>
                  <button type="button" @click="fetchQueues"
                    class="text-gray-400 hover:text-blue-500 transition-colors cursor-pointer">
                    <i class="fas fa-sync-alt text-[9px]" :class="{ 'animate-spin': queueLoading }"></i>
                  </button>
                </div>

                <div class="mb-4">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-[9px] font-black text-amber-500 uppercase">VIP</span>
                    <div class="h-[1px] flex-1 bg-amber-100"></div>
                  </div>
                  <ul class="space-y-1.5 max-h-32 overflow-y-auto custom-scrollbar pr-1">
                    <li v-for="q in vipQueues" :key="q.id"
                      class="flex items-center justify-between bg-amber-50/50 p-2 rounded-lg border border-amber-100/50">
                      <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-gray-800 truncate uppercase">{{ q.name }}</p>
                        <p class="text-[10px] font-bold text-amber-500 truncate">Credit : {{ q.wallet?.play_balance || 0
                        }}</p>
                      </div>
                      <div class="flex items-center gap-1.5 ml-2">
                        <template v-if="q.status === 'playing'">
                          <span :class="statusStyle(q.status)"
                            class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase">{{ q.status }}</span>
                          <button @click="cancelPlayingStatus(q)" type="button"
                            class="w-5 h-5 bg-red-100 text-red-600 rounded flex items-center justify-center hover:bg-red-200 cursor-pointer">
                            <i class="fas fa-undo text-[8px]"></i>
                          </button>
                        </template>
                        <span v-else-if="q.status !== 'pending'" :class="statusStyle(q.status)"
                          class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase">{{ q.status }}</span>
                        <template v-else>
                          <button @click="acceptQueue(q)" type="button"
                            class="w-5 h-5 bg-blue-500 text-white rounded shadow-sm flex items-center justify-center hover:bg-blue-600 cursor-pointer">
                            <i class="fas fa-check text-[8px]"></i>
                          </button>
                          <button @click="rejectQueue(q)" type="button"
                            class="w-5 h-5 bg-white text-red-500 border border-red-100 rounded shadow-sm flex items-center justify-center hover:bg-red-50 cursor-pointer">
                            <i class="fas fa-times text-[8px]"></i>
                          </button>
                        </template>
                      </div>
                    </li>
                    <li v-if="vipQueues.length === 0" class="text-[9px] text-gray-400 italic text-center py-1">Kosong
                    </li>
                  </ul>
                </div>

                <div>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-[9px] font-black text-indigo-500 uppercase">Public</span>
                    <div class="h-[1px] flex-1 bg-indigo-100"></div>
                  </div>
                  <ul class="space-y-1.5 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                    <li v-for="q in publicQueues" :key="q.id"
                      class="flex items-center justify-between bg-gray-50 p-2 rounded-lg border border-gray-100">
                      <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-gray-800 truncate uppercase"><span
                            class="text-gray-400">#{{ q.queue_number
                            }}</span> {{ q.nickname || q.name }}</p>
                      </div>
                      <div class="flex items-center gap-1.5 ml-2">
                        <template v-if="q.status === 'playing'">
                          <span :class="statusStyle(q.status)"
                            class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase">{{ q.status }}</span>
                          <button @click="cancelPlayingStatus(q)" type="button"
                            class="w-5 h-5 bg-red-100 text-red-600 rounded flex items-center justify-center hover:bg-red-200 cursor-pointer">
                            <i class="fas fa-undo text-[8px]"></i>
                          </button>
                        </template>
                        <span v-else-if="q.status !== 'pending'" :class="statusStyle(q.status)"
                          class="px-1.5 py-0.5 rounded text-[7px] font-black uppercase">{{ q.status }}</span>
                        <template v-else>
                          <button @click="acceptQueue(q)" type="button"
                            class="w-5 h-5 bg-blue-500 text-white rounded shadow-sm flex items-center justify-center hover:bg-blue-600 cursor-pointer">
                            <i class="fas fa-check text-[8px]"></i>
                          </button>
                          <button @click="rejectQueue(q)" type="button"
                            class="w-5 h-5 bg-white text-red-500 border border-red-100 rounded shadow-sm flex items-center justify-center hover:bg-red-50 cursor-pointer">
                            <i class="fas fa-times text-[8px]"></i>
                          </button>
                        </template>
                      </div>
                    </li>
                    <li v-if="publicQueues.length === 0" class="text-[9px] text-gray-400 italic text-center py-1">Kosong
                    </li>
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
      if (newVal) this.fetchQueues();
    }
  },
  async mounted() {
    await this.fetchInitialData();
    const urlId = this.$route.params.id;
    if (urlId) {
      this.form.schedule_id = parseInt(urlId);
      const currentSchedule = this.schedules.find(s => s.id == urlId);
      if (currentSchedule?.season_id) this.form.season_id = currentSchedule.season_id;
    }
    this.queueTimer = setInterval(() => {
      if (this.form.schedule_id) this.fetchQueues();
    }, 30000);
  },
  beforeUnmount() {
    if (this.queueTimer) clearInterval(this.queueTimer);
  },
  methods: {
    acceptQueue(q) {
      const playerId = q.player_id || q.id;
      const playerName = q.name || q.nickname;
      const alreadyInForm = this.form.players.some(p => p.player_id === playerId);

      if (alreadyInForm) {
        alert("Pemain ini sudah ada di dalam form lineup!");
        return;
      }

      let emptySlot = this.form.players.find(p => p.player_id === '');
      if (!emptySlot) {
        this.addPlayer();
        emptySlot = this.form.players[this.form.players.length - 1];
      }

      emptySlot.player_id = playerId;
      emptySlot.player_search_text = playerName;
      this.updateQueueStatus(q.id, 'playing');
    },

    async cancelPlayingStatus(q) {
      const playerName = q.name || q.nickname;
      const playerId = q.player_id || q.id;

      if (confirm(`Batalkan status 'Playing' untuk ${playerName}?`)) {
        try {
          await this.updateQueueStatus(q.id, 'pending');

          const playerIdx = this.form.players.findIndex(p => p.player_id === playerId);
          if (playerIdx !== -1) {
            this.form.players[playerIdx].player_id = '';
            this.form.players[playerIdx].player_search_text = '';
            this.form.players[playerIdx].hero_id = '';
            this.form.players[playerIdx].hero_search_text = '';
            this.form.players[playerIdx].role_id = '';
          }
        } catch (e) {
          alert("Gagal membatalkan status");
        }
      }
    },

    rejectQueue(q) {
      const playerName = q.name || q.nickname;
      if (confirm(`Skip / Tolak ${playerName}?`)) {
        this.updateQueueStatus(q.id, 'skipped');
      }
    },

    async updateQueueStatus(queueId, status) {
      try {
        await axios.patch(`/api/v1/queues/${queueId}/status`, { status });
        this.fetchQueues();
      } catch (e) {
        console.error("Gagal mengubah status antrean:", e);
        throw e;
      }
    },

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
        case 'playing': return 'bg-emerald-500 text-white';
        case 'completed': return 'bg-gray-200 text-gray-500';
        case 'skipped': return 'bg-red-100 text-red-600';
        default: return 'bg-blue-100 text-blue-700';
      }
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

    getAvailableRoles(currentIndex) {
      const selectedRoleIds = this.form.players
        .filter((_, index) => index !== currentIndex)
        .map(p => p.role_id)
        .filter(id => id !== '');
      return this.roles.filter(r => !selectedRoleIds.includes(r.id) || r.id === this.form.players[currentIndex].role_id);
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
        const allQueues = [...this.vipQueues, ...this.publicQueues];
        const updatePromises = this.form.players.map(formPlayer => {
          const targetQueue = allQueues.find(q => {
            const idInQueue = q.player_id || q.id;
            return String(idInQueue) === String(formPlayer.player_id);
          });

          if (targetQueue) {
            console.log(`Updating status for: ${targetQueue.name || targetQueue.nickname} (ID: ${targetQueue.id})`);
            return axios.patch(`/api/v1/queues/${targetQueue.id}/status`, {
              status: 'completed'
            });
          } else {
            console.warn(`Pemain dengan ID ${formPlayer.player_id} tidak ditemukan di list antrean.`);
            return Promise.resolve();
          }
        });
        await Promise.all(updatePromises);

        alert("Berhasil! Semua pemain telah diupdate ke status Completed.");
        this.$router.push(`/schedules/${this.form.schedule_id}`);

      } catch (error) {
        console.error("Detail Error:", error);
        alert("Gagal: " + (error.response?.data?.message || "Terjadi kesalahan saat finalisasi match"));
      } finally {
        this.submitting = false;
      }
    },
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 10px;
}
</style>