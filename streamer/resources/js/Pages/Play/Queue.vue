<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans text-slate-900">
    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      
      <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
            <i class="fas fa-users-viewfinder text-white text-xl"></i>
          </div>
          <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Public Queue</h1>
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest">Pantau & Join Antrean Mabar</p>
          </div>
        </div>
        
        <div v-if="selectedStreamerName" class="flex flex-col items-end">
          <span class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Watching Streamer</span>
          <span class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 italic">
            @{{ selectedStreamerName }}
          </span>
        </div>
      </header>

      <div class="p-8 max-w-7xl mx-auto w-full">
        
        <section class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="text-[11px] font-black text-gray-400 uppercase ml-1 tracking-widest">1. Cari Streamer Favorit</label>
            <div class="relative group">
              <select v-model="form.user_id" @change="fetchSchedules" 
                class="w-full px-6 py-4 rounded-2xl border border-gray-200 bg-white text-gray-900 font-bold outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 transition-all appearance-none cursor-pointer shadow-sm">
                <option value="" disabled>Pilih Nama Streamer...</option>
                <option v-for="s in streamers" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
              <i class="fas fa-search absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 group-hover:text-blue-500 transition-colors"></i>
            </div>
          </div>

          <div v-if="schedules.length > 0" class="space-y-2 animate-in fade-in duration-500">
            <label class="text-[11px] font-black text-gray-400 uppercase ml-1 tracking-widest">2. Pilih Sesi Live</label>
            <div class="relative group">
              <select v-model="form.schedule_id" @change="handleScheduleChange"
                class="w-full px-6 py-4 rounded-2xl border-2 border-blue-100 bg-blue-50/30 text-blue-700 font-bold outline-none focus:ring-4 focus:ring-blue-500/5 focus:border-blue-600 transition-all appearance-none cursor-pointer shadow-sm">
                <option value="" disabled>Pilih Jadwal...</option>
                <option v-for="sch in schedules" :key="sch.id" :value="sch.id">{{ sch.title }} ({{ sch.status }})</option>
              </select>
              <i class="fas fa-calendar-check absolute right-6 top-1/2 -translate-y-1/2 text-blue-400"></i>
            </div>
          </div>
        </section>

        <div v-if="form.schedule_id" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start animate-in slide-in-from-bottom-4 duration-700">
          
          <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-800 italic">Live Queue Monitor</h2>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Real-time Update</span>
              </div>
            </div>

            <div v-if="loadingQueue" class="p-20 flex justify-center bg-white rounded-[2rem] border border-gray-100 shadow-sm">
              <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full"></div>
            </div>

            <div v-else class="grid gap-3">
              <div v-if="queues.length === 0" class="py-24 text-center bg-white rounded-[2.5rem] border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                  <i class="fas fa-ghost text-gray-300 text-2xl"></i>
                </div>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Belum ada pemain dalam antrean</p>
              </div>

              <div v-for="item in queues" :key="item.id" 
                class="group flex items-center gap-5 p-4 rounded-2xl border transition-all duration-300"
                :class="item.status === 'playing' ? 'bg-blue-600 border-blue-400 shadow-xl shadow-blue-100' : 'bg-white border-gray-100 hover:border-blue-200 hover:shadow-md'">
                
                <div class="w-14 h-14 rounded-xl flex items-center justify-center font-black text-xl"
                  :class="item.status === 'playing' ? 'bg-white/20 text-white shadow-inner' : 'bg-gray-50 text-gray-400'">
                  {{ item.queue_number }}
                </div>

                <div class="flex-grow">
                  <h4 class="font-black uppercase tracking-tight text-sm" :class="item.status === 'playing' ? 'text-white' : 'text-gray-900'">
                    {{ item.nickname }}
                  </h4>
                  <div class="flex gap-2 mt-1">
                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded" 
                      :class="item.status === 'playing' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600'">
                      Server {{ item.mlbb_server }}
                    </span>
                  </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                  <span class="text-[9px] font-black uppercase px-3 py-1 rounded-full border tracking-tighter"
                    :class="item.status === 'playing' ? 'bg-emerald-400 border-emerald-300 text-white' : 'bg-gray-50 border-gray-100 text-gray-500'">
                    {{ item.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <aside class="space-y-6 sticky top-28">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 relative overflow-hidden">
              <div class="relative z-10 text-center">
                <h3 class="text-lg font-black uppercase text-gray-900 mb-1">Daftar Mabar</h3>
                <p class="text-[10px] text-gray-400 font-bold mb-8 uppercase tracking-widest">Konfirmasi Akun MLBB Anda</p>

                <div class="space-y-4">
                  <div class="grid grid-cols-1 gap-3">
                    <input v-model="form.mlbb_id" placeholder="User ID" class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 text-gray-900 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none transition-all shadow-inner" />
                    <input v-model="form.mlbb_server" placeholder="Zone ID" class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 text-gray-900 text-sm font-bold focus:bg-white focus:border-blue-500 outline-none transition-all shadow-inner" />
                  </div>

                  <button @click="fetchNickname" :disabled="loadingNickname || !form.mlbb_id"
                    class="w-full py-4 bg-gray-900 hover:bg-black text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all active:scale-95 disabled:opacity-50">
                    <i v-if="loadingNickname" class="fas fa-spinner animate-spin mr-2"></i>
                    {{ loadingNickname ? 'Verifikasi...' : 'Cek Nickname' }}
                  </button>

                  <div v-if="form.nickname" class="pt-4 animate-in zoom-in duration-300">
                    <div class="p-5 bg-emerald-50 border border-emerald-100 rounded-2xl mb-4 text-center">
                      <span class="text-[9px] text-emerald-600 font-black uppercase block mb-1">Pemain Ditemukan</span>
                      <span class="text-xl font-black text-emerald-700 uppercase italic tracking-tight">{{ form.nickname }}</span>
                    </div>

                    <div class="mb-4 bg-gray-50 p-3 rounded-2xl border border-gray-100 shadow-inner">
                      <div class="flex items-center justify-between mb-2 bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex-grow flex justify-center">
                          <img v-if="captchaUrl" :src="captchaUrl" alt="Captcha" class="h-9 rounded select-none pointer-events-none" />
                        </div>
                        <button @click="loadCaptcha" type="button" class="text-blue-600 p-2 hover:bg-blue-50 rounded-full transition-colors">
                          <i class="fas fa-sync-alt text-xs"></i>
                        </button>
                      </div>
                      <input v-model="captchaInput" placeholder="KODE CAPTCHA" maxlength="5"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-600 outline-none uppercase text-center tracking-widest font-black text-sm" />
                    </div>

                    <button @click="handleSubmit" :disabled="submitting || !captchaInput"
                      class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-200 uppercase tracking-[0.2em] text-xs transition-all active:scale-95 disabled:opacity-50">
                      {{ submitting ? 'Memproses...' : 'Ambil Antrean Sekarang' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-6 bg-blue-50 rounded-3xl border border-blue-100">
              <h4 class="text-[10px] font-black text-blue-600 uppercase mb-3 flex items-center gap-2">
                <i class="fas fa-shield-halved"></i> Sistem Antrean
              </h4>
              <p class="text-[11px] text-blue-900/60 font-medium leading-relaxed italic">
                Nomor antrean diberikan otomatis. Harap standby di lobby game MLBB Anda.
              </p>
            </div>
          </aside>
        </div>

        <div v-else class="py-32 text-center animate-in zoom-in duration-500">
          <div class="w-24 h-24 bg-white rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-xl shadow-gray-200">
            <i class="fas fa-video text-3xl text-blue-600/30"></i>
          </div>
          <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tight">Menunggu Pilihan Anda</h3>
          <p class="text-gray-400 text-sm mt-3 font-medium max-w-sm mx-auto leading-relaxed">
            Silakan pilih streamer dan sesi streaming untuk bergabung.
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      streamers: [],
      schedules: [],
      queues: [],
      captchaUrl: '',
      captchaInput: '',
      loadingNickname: false,
      loadingQueue: false,
      submitting: false,
      refreshInterval: null,
      form: {
        user_id: '',
        schedule_id: '',
        nickname: '',
        mlbb_id: '',
        mlbb_server: ''
      }
    };
  },
  computed: {
    selectedStreamerName() {
      const s = this.streamers.find(st => st.id === this.form.user_id);
      return s ? s.name : '';
    }
  },
  mounted() {
    this.fetchStreamers();
    this.loadCaptcha();
    this.refreshInterval = setInterval(() => {
      if (this.form.schedule_id) this.fetchQueue();
    }, 20000);
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    loadCaptcha() {
      const baseUrl = "http://localhost:8000/api/captcha";
      this.captchaUrl = `${baseUrl}?t=${new Date().getTime()}`;
      this.captchaInput = '';
    },
    async fetchStreamers() {
      try {
        const res = await axios.get('/api/v1/public/streamers');
        this.streamers = res.data.data;
      } catch (e) { console.error("Gagal load streamers"); }
    },
    async fetchSchedules() {
      this.schedules = [];
      this.form.schedule_id = '';
      this.queues = [];
      try {
        const res = await axios.get('/api/v1/public/schedules', { params: { user_id: this.form.user_id } });
        this.schedules = res.data.data;
      } catch (e) { console.error("Gagal load sesi"); }
    },
    handleScheduleChange() {
      this.form.nickname = '';
      this.fetchQueue();
    },
    async fetchNickname() {
      if (!this.form.mlbb_id || !this.form.mlbb_server) {
        alert("Isi User ID dan Zone ID!");
        return;
      }
      this.loadingNickname = true;
      try {
        const res = await axios.get('/api/v1/mlbb-nickname', { params: { id: this.form.mlbb_id, zone: this.form.mlbb_server } });
        if (res.data.success) {
          this.form.nickname = res.data.name;
        } else {
          alert("ID MLBB tidak ditemukan!");
        }
      } catch (e) {
        alert("Terjadi kesalahan pengecekan ID");
      } finally { this.loadingNickname = false; }
    },
    async fetchQueue() {
      if (!this.form.schedule_id) return;
      this.loadingQueue = true;
      try {
        const res = await axios.get('/api/v1/public/queue-list', { params: { schedule_id: this.form.schedule_id } });
        this.queues = res.data.data;
      } finally { this.loadingQueue = false; }
    },
    async handleSubmit() {
      if (!this.captchaInput) {
        alert("Harap isi captcha!");
        return;
      }
      this.submitting = true;
      try {
        const res = await axios.post('/api/v1/public/join-queue', {
          ...this.form,
          captcha: this.captchaInput
        });
        alert(`Berhasil! Nomor Antrean Anda: #${res.data.data.queue_number}`);
        this.form.nickname = '';
        this.form.mlbb_id = '';
        this.form.mlbb_server = '';
        this.captchaInput = '';
        this.loadCaptcha();
        this.fetchQueue();
      } catch (e) {
        alert(e.response?.data?.message || "Gagal mendaftar");
        this.loadCaptcha();
      } finally { this.submitting = false; }
    }
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.animate-in { animation: fadeIn 0.5s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>