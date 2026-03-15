<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'players'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()"
            class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95 cursor-pointer">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Tambah Pemain Baru</h1>
            <p class="text-xs text-gray-500 font-medium italic">Daftarkan anggota baru ke dalam database</p>
          </div>
        </div>
      </header>

      <div class="p-6">
        <div class="max-w-4xl mx-auto">
          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2
                  class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-purple-500 pl-3">
                  Informasi Identitas
                </h2>

                <div class="space-y-5">
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">Nama /
                      Nickname</label>
                    <input v-model="form.name" type="text" placeholder="Masukkan nama pemain..."
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 outline-none text-sm font-bold transition-all"
                      required />
                  </div>

                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">Tipe
                      Keanggotaan</label>
                    <div class="grid grid-cols-2 gap-4">
                      <label
                        :class="form.type === 'PUBLIC' ? 'border-purple-500 bg-purple-50/50 ring-1 ring-purple-500' : 'border-gray-100 hover:bg-gray-50'"
                        class="relative p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col items-center gap-2">
                        <input type="radio" v-model="form.type" value="PUBLIC" class="hidden" />
                        <i class="fas fa-globe text-lg"
                          :class="form.type === 'PUBLIC' ? 'text-purple-600' : 'text-gray-300'"></i>
                        <span class="text-xs font-black uppercase tracking-tighter"
                          :class="form.type === 'PUBLIC' ? 'text-purple-700' : 'text-gray-400'">Public</span>
                      </label>

                      <label
                        :class="form.type === 'VIP' ? 'border-amber-500 bg-amber-50/50 ring-1 ring-amber-500' : 'border-gray-100 hover:bg-gray-50'"
                        class="relative p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col items-center gap-2">
                        <input type="radio" v-model="form.type" value="VIP" class="hidden" />
                        <i class="fas fa-crown text-lg"
                          :class="form.type === 'VIP' ? 'text-amber-500' : 'text-gray-300'"></i>
                        <span class="text-xs font-black uppercase tracking-tighter"
                          :class="form.type === 'VIP' ? 'text-amber-700' : 'text-gray-400'">VIP Member</span>
                      </label>
                    </div>
                  </div>
                  <div v-if="form.type === 'VIP'" class="mt-5 animate-in fade-in duration-500">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">
                      Credit Awal (Optional)
                    </label>
                    <div class="relative">
                      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Credit : </span>
                      <input v-model.number="form.play_balance" type="number" min="0" placeholder="Masukkan credit awal untuk pemain VIP..."
                        class="w-full pl-18 pr-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 outline-none text-sm font-bold transition-all" />
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="form.type === 'VIP'" class="bg-amber-50 border border-amber-100 p-5 rounded-2xl flex gap-4">
                <div
                  class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm text-amber-500">
                  <i class="fas fa-wallet"></i>
                </div>
                <div>
                  <h4 class="text-[11px] font-black text-amber-800 uppercase tracking-wider">Automated Wallet Creation
                  </h4>
                  <p class="text-[10px] text-amber-700/80 leading-relaxed font-medium mt-0.5">
                    Pemain dengan tipe <b>VIP</b> akan secara otomatis dibuatkan sistem dompet (wallet).
                  </p>
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 text-center">Konfirmasi</h3>

                <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
                  <div class="flex justify-between text-[10px] uppercase font-bold tracking-tight">
                    <span class="text-gray-400">Status</span>
                    <span class="text-green-600">New Entry</span>
                  </div>
                  <div class="flex justify-between text-[10px] uppercase font-bold tracking-tight">
                    <span class="text-gray-400">Created By</span>
                    <span class="text-gray-900">{{ players || 'Unknown Streamer' }}</span>
                  </div>
                </div>

                <button type="submit" :disabled="submitting || !form.name"
                  class="cursor-pointer w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50">
                  <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
                  <span>{{ submitting ? 'MENYIMPAN...' : 'SIMPAN PEMAIN' }}</span>
                </button>

                <button type="button" @click="$router.back()"
                  class="w-full mt-3 py-3 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors">
                  BATAL
                </button>
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
      players: null, // Default saat fetching
      form: {
        name: '',
        type: 'PUBLIC',
        play_balance: 0
      }
    }
  },
  mounted() {
    this.getStreamerInfo();
  },
  methods: {
    async getStreamerInfo() {
      try {
        const response = await axios.get('/api/v1/players');
        const playersList = response.data.data;
        this.players = playersList[0].user.name;
      } catch (error) {
        console.error("Error fetching info:", error);
        this.players = "Error Load Name";
      }
    },
    async handleSubmit() {
      if (!this.form.name) return;
      this.submitting = true;
      try {
        await axios.post('/api/v1/players', {
          name: this.form.name,
          type: this.form.type,
          play_balance: this.form.type === 'VIP' ? this.form.play_balance : 0
        });
        alert("Pemain berhasil ditambahkan!");
        this.$router.push('/dashboard');
      } catch (error) {
        alert("Gagal: " + (error.response?.data?.message || "Terjadi kesalahan"));
      } finally {
        this.submitting = false;
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
  background: #e2e8f0;
  border-radius: 10px;
}
</style>