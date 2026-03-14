<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'matches'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
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
                <h2 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-blue-500 pl-3">
                  Informasi Pertandingan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Season</label>
                    <select v-model="form.season_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm font-medium" required>
                      <option v-for="s in seasons" :key="s.id" :value="s.id">Season {{ s.name }}</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Jadwal Stream</label>
                    <select v-model="form.schedule_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm font-medium" required>
                      <option v-for="sch in schedules" :key="sch.id" :value="sch.id">{{ sch.title }}</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-orange-500 pl-3">
                  Lineup Pemain (Team)
                </h2>
                
                <div class="space-y-4">
                  <div v-for="(player, index) in form.players" :key="index" 
                       class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    
                    <div>
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Player {{ index + 1 }}</label>
                      <select v-model="player.player_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500">
                        <option v-for="p in playersList" :key="p.id" :value="p.id">{{ p.name }} ({{ p.type }})</option>
                      </select>
                    </div>

                    <div>
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Hero</label>
                      <select v-model="player.hero_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500">
                        <option v-for="h in heroes" :key="h.id" :value="h.id">{{ h.name }}</option>
                      </select>
                    </div>

                    <div>
                      <label class="text-[9px] font-black text-gray-400 uppercase mb-1 block">Role</label>
                      <select v-model="player.role_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold shadow-sm outline-none focus:border-blue-500">
                        <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
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
                    <span class="text-gray-900 font-bold">{{ form.players.length }} / 5</span>
                  </div>
                  <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full transition-all" :style="{ width: (form.players.length / 5 * 100) + '%' }"></div>
                  </div>
                </div>

                <button 
                  type="submit" 
                  :disabled="submitting"
                  class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50"
                >
                  <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
                  <span>{{ submitting ? 'MEMPROSES...' : 'POST MATCH DATA' }}</span>
                </button>
                
                <button type="button" @click="$router.back()" class="w-full mt-3 py-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors">
                  BATAL & KEMBALI
                </button>
              </div>

              <div class="bg-orange-50 border border-orange-100 p-4 rounded-2xl">
                <div class="flex gap-3">
                  <i class="fas fa-exclamation-triangle text-orange-500"></i>
                  <p class="text-[10px] text-orange-700 leading-relaxed font-medium">
                    Sistem akan otomatis memotong balance pemain berstatus <b>VIP</b> sebanyak 1 point setiap match yang diinput.
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
      // Data Dropdown (Biasanya diambil dari API saat mounted)
      seasons: [],
      schedules: [],
      playersList: [],
      heroes: [],
      roles: [],
      
      form: {
        season_id: '',
        schedule_id: '',
        players: Array.from({ length: 5 }, () => ({
          player_id: '',
          hero_id: '',
          role_id: ''
        }))
      }
    }
  },
  async mounted() {
    this.fetchInitialData();
  },
  methods: {
    async fetchInitialData() {
      try {
        const [s, sch, p, h, r] = await Promise.all([
          axios.get('/api/v1/seasons'),
          axios.get('/api/v1/schedules'),
          axios.get('/api/v1/players'),
          axios.get('/api/v1/heroes'),
          axios.get('/api/v1/roles')
        ]);
        this.seasons = s.data.data;
        this.schedules = sch.data.data.data;
        this.playersList = p.data.data;
        this.heroes = h.data.data;
        this.roles = r.data.data;
      } catch (err) {
        console.error("Gagal memuat data referensi", err);
      }
    },
    async handleSubmit() {
      this.submitting = true;
      try {
        await axios.post('/api/v1/matches', this.form);
        this.$router.push('/matches'); 
      } catch (error) {
        const msg = error.response?.data?.message || "Terjadi kesalahan";
        alert("Gagal: " + msg);
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>