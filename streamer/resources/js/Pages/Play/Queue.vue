<template>
  <div class="min-h-screen bg-slate-50 font-sans pb-12">
    <div class="bg-gray-900 text-white pt-12 pb-20 px-6 rounded-b-[40px] shadow-2xl">
      <div class="max-w-md mx-auto text-center">
        <div class="inline-block p-3 bg-blue-500/20 rounded-2xl mb-4 backdrop-blur-sm">
          <i class="fas fa-gamepad text-2xl text-blue-400"></i>
        </div>
        <h1 class="text-2xl font-black italic tracking-tighter uppercase">Join Antrean Mabar</h1>
        <p class="text-gray-400 text-xs mt-2 font-medium uppercase tracking-widest">Gratis untuk seluruh follower</p>
      </div>
    </div>

    <div class="max-w-md mx-auto px-6 -mt-12">
      <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          
          <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
            <label class="text-[10px] font-black text-blue-500 uppercase mb-3 block tracking-widest">Data Akun Mobile Legends</label>
            <div class="grid grid-cols-3 gap-3">
              <div class="col-span-2">
                <input v-model="form.mlbb_id" type="text" placeholder="User ID" 
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:border-blue-500 outline-none transition-all font-mono" required />
              </div>
              <div>
                <input v-model="form.mlbb_server" type="text" placeholder="Zone" 
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:border-blue-500 outline-none transition-all font-mono" required />
              </div>
            </div>
            
            <button type="button" @click="fetchNickname" :disabled="loadingNickname || !form.mlbb_id || !form.mlbb_server"
              class="w-full mt-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all disabled:opacity-50 shadow-lg shadow-blue-200">
              <i v-if="loadingNickname" class="fas fa-spinner animate-spin mr-2"></i>
              <span v-else>Cek Nickname Otomatis</span>
            </button>
          </div>

          <div>
            <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block tracking-widest">Nickname Terdeteksi</label>
            <div class="relative">
              <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
              <input v-model="form.nickname" type="text" placeholder="Nickname akan muncul di sini..."
                class="w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-gray-50 focus:border-blue-500 outline-none text-sm font-black transition-all bg-gray-50/50"
                readonly required />
            </div>
            <p class="text-[9px] text-gray-400 mt-2 italic text-center leading-relaxed">
              *Pastikan nickname sudah sesuai sebelum menekan tombol join.
            </p>
          </div>

          <div class="pt-4">
            <button type="submit" :disabled="submitting || !form.nickname"
              class="w-full bg-gray-900 hover:bg-black text-white font-black py-5 rounded-2xl transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-20 shadow-xl shadow-gray-200 uppercase tracking-widest text-xs">
              <i v-if="submitting" class="fas fa-spinner animate-spin"></i>
              <span>{{ submitting ? 'Memproses...' : 'Daftar Antrean Sekarang' }}</span>
            </button>
          </div>
        </form>
      </div>

      <div class="mt-8 text-center space-y-2">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Powered by Isan MLBB API</p>
        <div class="flex justify-center gap-4 text-gray-300">
          <i class="fab fa-tiktok"></i>
          <i class="fab fa-youtube"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      submitting: false,
      loadingNickname: false,
      form: {
        nickname: '',
        mlbb_id: '',
        mlbb_server: '',
        schedule_id: this.$route.params.id // Mengambil ID jadwal dari URL
      }
    }
  },
  methods: {
    async fetchNickname() {
      this.loadingNickname = true;
      try {
        // Panggil endpoint proxy yang sudah dibuat sebelumnya (Public)
        const response = await axios.get('/api/v1/mlbb-nickname', {
          params: { id: this.form.mlbb_id, zone: this.form.mlbb_server }
        });
        
        if (response.data && response.data.name) {
          this.form.nickname = response.data.name;
        }
      } catch (error) {
        alert("ID/Server tidak ditemukan. Silakan cek kembali.");
      } finally {
        this.loadingNickname = false;
      }
    },

    async handleSubmit() {
      this.submitting = true;
      try {
        // Kirim ke endpoint Queue Public
        await axios.post('/api/v1/public/join-queue', this.form);
        alert("Berhasil! Kamu sudah masuk dalam antrean.");
        // Optional: Redirect ke halaman sukses atau list antrean
      } catch (error) {
        alert(error.response?.data?.message || "Gagal masuk antrean.");
      } finally {
        this.submitting = false;
      }
    }
  }
}
</script>