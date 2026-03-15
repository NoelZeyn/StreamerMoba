<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'matches'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Input Hasil Match</h1>
            <p class="text-xs text-gray-500 font-medium italic">Catat detail pertandingan dan lineup hero</p>
          </div>
        </div>
      </header>

      <div class="p-6">
        <div class="max-w-5xl mx-auto">
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
                    <div v-if="$route.query.title"
                      class="px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-bold text-gray-700 flex justify-between items-center">
                      {{ $route.query.title }}
                      <i class="fas fa-lock text-gray-300 text-xs"></i>
                    </div>
                    <select v-else v-model="form.schedule_id"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm font-medium"
                      required>
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
                    class="relative p-4 rounded-xl border border-gray-100 bg-gray-50/50 grid grid-cols-1 md:grid-cols-3 gap-3 items-end group">

                    <button v-if="form.players.length > 1" type="button" @click="removePlayer(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-white border border-red-100 text-red-500 rounded-full shadow-sm flex items-center justify-center hover:bg-red-500 hover:text-white transition-all z-10">
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
                          {{ p.name }} <span class="text-gray-400 font-normal">({{ p.type }})</span>
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
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
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
                  class="w-full mt-3 py-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors">
                  BATAL & KEMBALI
                </button>
              </div>

              <div class="bg-orange-50 border border-orange-100 p-4 rounded-2xl">
                <div class="flex gap-3">
                  <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                  <p class="text-[10px] text-orange-700 leading-relaxed font-medium">
                    Sistem akan otomatis memotong balance pemain berstatus <b>VIP</b> sebanyak 1 point setiap match yang
                    diinput.
                  </p>
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
      }
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
    }
  },
  async mounted() {
    await this.fetchInitialData();

    // Auto-fill dari query parameter (Schedule Detail)
    if (this.$route.query.schedule_id) {
      this.form.schedule_id = parseInt(this.$route.query.schedule_id);
    }
    if (this.$route.query.season_id) {
      this.form.season_id = parseInt(this.$route.query.season_id);
    }
  },
  methods: {
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