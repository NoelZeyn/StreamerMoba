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
            <p class="text-xs text-gray-500 font-medium italic">Hubungkan ID MLBB untuk fitur Topup Otomatis</p>
          </div>
        </div>
      </header>

      <div class="p-6">
        <div class="max-w-4xl mx-auto">
          <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2
                  class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-blue-500 pl-3">
                  Data Game & Identitas
                </h2>

                <div class="space-y-5">
                  <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 mb-2">
                    <label class="text-[10px] font-bold text-blue-400 uppercase mb-3 block tracking-wider">Cari via MLBB
                      ID (Opsional)</label>
                    <div class="grid grid-cols-2 gap-3">
                      <div class="relative">
                        <input v-model="form.mlbb_id" type="text" placeholder="User ID"
                          class="w-full pl-3 pr-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:border-blue-500 outline-none transition-all font-mono" />
                      </div>
                      <div class="relative">
                        <input v-model="form.mlbb_server" type="text" placeholder="Zone ID"
                          class="w-full pl-3 pr-3 py-2.5 rounded-lg border border-gray-200 bg-white text-sm focus:border-blue-500 outline-none transition-all font-mono" />
                      </div>
                    </div>
                    <button type="button" @click="fetchNickname"
                      :disabled="loadingNickname || !form.mlbb_id || !form.mlbb_server"
                      class="w-full mt-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                      <i v-if="loadingNickname" class="fas fa-spinner animate-spin mr-2"></i>
                      <i v-else class="fas fa-search mr-2"></i>
                      {{ loadingNickname ? 'Mengecek Database...' : 'Ambil Nickname Otomatis' }}
                    </button>
                  </div>

                  <hr class="border-gray-100" />

                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">Nama /
                      Nickname di Sistem</label>
                    <input v-model="form.name" type="text" placeholder="Masukkan nama pemain..."
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-purple-500 outline-none text-sm font-bold transition-all bg-white"
                      required />
                    <p class="text-[9px] text-gray-400 mt-1 italic">*Jika menggunakan Saweria, pastikan nama ini sama
                      dengan nama donatur.</p>
                  </div>

                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">Tipe
                      Keanggotaan</label>
                    <div class="grid grid-cols-2 gap-4">
                      <label
                        :class="form.type === 'PUBLIC' ? 'border-purple-500 bg-purple-50 ring-1 ring-purple-500' : 'border-gray-100 hover:bg-gray-50 bg-white'"
                        class="relative p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col items-center gap-2">
                        <input type="radio" v-model="form.type" value="PUBLIC" class="hidden" />
                        <i class="fas fa-globe text-lg"
                          :class="form.type === 'PUBLIC' ? 'text-purple-600' : 'text-gray-300'"></i>
                        <span class="text-xs font-black uppercase tracking-tighter"
                          :class="form.type === 'PUBLIC' ? 'text-purple-700' : 'text-gray-400'">Public</span>
                      </label>

                      <label
                        :class="form.type === 'VIP' ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-100 hover:bg-gray-50 bg-white'"
                        class="relative p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col items-center gap-2">
                        <input type="radio" v-model="form.type" value="VIP" class="hidden" />
                        <i class="fas fa-crown text-lg"
                          :class="form.type === 'VIP' ? 'text-amber-500' : 'text-gray-300'"></i>
                        <span class="text-xs font-black uppercase tracking-tighter"
                          :class="form.type === 'VIP' ? 'text-amber-700' : 'text-gray-400'">VIP Member</span>
                      </label>
                    </div>
                  </div>

                  <div v-if="form.type === 'VIP'" class="mt-5 animate-in slide-in-from-top-2 duration-300">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-wider">Credit
                      Awal</label>
                    <div class="relative">
                      <span
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs uppercase">Credit
                        : </span>
                      <input v-model.number="form.play_balance" type="number" min="0" placeholder="0"
                        class="w-full pl-20 pr-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 outline-none text-sm font-bold transition-all bg-white" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 text-center">Ringkasan</h3>

                <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
                  <div class="flex justify-between text-[10px] uppercase font-bold tracking-tight">
                    <span class="text-gray-400">Streamer</span>
                    <span class="text-gray-900">{{ streamerName || 'Loading...' }}</span>
                  </div>
                  <div class="flex justify-between text-[10px] uppercase font-bold tracking-tight">
                    <span class="text-gray-400">Wallet Status</span>
                    <span :class="form.type === 'VIP' ? 'text-amber-600' : 'text-gray-400'">
                      {{ form.type === 'VIP' ? 'Auto-Generate' : 'None' }}
                    </span>
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

              <div v-if="form.type === 'VIP'"
                class="bg-amber-900 text-white p-5 rounded-2xl shadow-lg shadow-amber-200/20">
                <div class="flex items-start gap-4">
                  <i class="fas fa-bolt text-amber-400 mt-1"></i>
                  <div>
                    <h4 class="text-[11px] font-black uppercase tracking-wider">Saweria Ready</h4>
                    <p class="text-[10px] text-amber-100/80 leading-relaxed mt-1">
                      Pemain ini sekarang bisa melakukan topup otomatis melalui Saweria dengan format nama atau ID MLBB
                      yang sesuai.
                    </p>
                  </div>
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
      loadingNickname: false,
      streamerName: '',
      form: {
        name: '',
        type: 'PUBLIC',
        play_balance: 0,
        mlbb_id: '',
        mlbb_server: ''
      }
    }
  },
  mounted() {
    this.fetchUserInfo();
  },
  methods: {
    async fetchUserInfo() {
      try {
        const response = await axios.get('/api/v1/players');
        if (response.data.data.length > 0) {
          this.streamerName = response.data.data[0].user.name;
        }
      } catch (error) {
        console.error("Gagal memuat info streamer:", error);
        this.streamerName = "System Admin";
      }
    },
    async fetchNickname() {
      if (!this.form.mlbb_id || !this.form.mlbb_server) return;

      this.loadingNickname = true;
      try {
        const response = await axios.get('/api/v1/mlbb-nickname', {
          params: {
            id: this.form.mlbb_id,
            zone: this.form.mlbb_server
          }
        });

        // API Isan me-return success: true/false
        if (response.data && response.data.success) {
          this.form.name = response.data.name;
          this.form.mlbb_id = response.data.id;
          this.form.mlbb_server = response.data.server;
        } else {
          alert("Nickname tidak ditemukan. Cek kembali ID & Server.");
        }
      } catch (error) {
        const errorMsg = error.response?.data?.message || "Terjadi kesalahan sistem.";
        alert("Error: " + errorMsg);
      } finally {
        this.loadingNickname = false;
      }
    },

    async handleSubmit() {
      if (!this.form.name) return;

      this.submitting = true;
      try {
        const payload = {
          name: this.form.name,
          type: this.form.type,
          play_balance: this.form.type === 'VIP' ? this.form.play_balance : 0,
          mlbb_id: String(this.form.mlbb_id),
          mlbb_server: String(this.form.mlbb_server)
        };

        await axios.post('/api/v1/players', payload);

        // Success feedback
        alert("Pemain " + this.form.name + " berhasil didaftarkan!");
        this.$router.push('/dashboard');
      } catch (error) {
        const errorMsg = error.response?.data?.message || "Terjadi kesalahan pada server.";
        alert("Gagal: " + errorMsg);
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

/* Chrome, Safari, Edge, Opera - hilangkan spin button number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>