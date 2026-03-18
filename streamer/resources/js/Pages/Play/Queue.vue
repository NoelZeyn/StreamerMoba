<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden font-sans text-slate-900">
    <Toast ref="toastRef" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-slate-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-100">
            <i class="fas fa-gamepad text-white text-lg"></i>
          </div>
          <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Mabar Queue</h1>
            <div v-if="selectedStreamerName" class="flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              <p class="text-xs font-medium text-indigo-600">@{{ selectedStreamerName }}</p>
            </div>
          </div>
        </div>
        
        <div class="hidden sm:flex bg-slate-100 p-1 rounded-lg text-xs font-semibold">
          <button class="px-3 py-1.5 bg-white shadow-sm rounded-md text-slate-700">List Antrean</button>
        </div>
      </header>

      <div class="p-4 md:p-6 max-w-5xl mx-auto w-full">
        <section class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div class="relative">
            <select v-model="form.user_id" @change="fetchSchedules" 
              class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
              <option value="" disabled>Pilih Streamer</option>
              <option v-for="s in streamers" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
          </div>

          <div v-if="schedules.length > 0" class="relative animate-in fade-in">
            <select v-model="form.schedule_id" @change="handleScheduleChange"
              class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-indigo-100 bg-indigo-50/50 text-indigo-700 text-sm font-bold outline-none focus:ring-2 focus:ring-indigo-500/20 appearance-none cursor-pointer">
              <option value="" disabled>Pilih Sesi Live</option>
              <option v-for="sch in schedules" :key="sch.id" :value="sch.id">{{ sch.title }}</option>
            </select>
            <i class="fas fa-calendar-alt absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400 text-[10px] pointer-events-none"></i>
          </div>
        </section>

        <div v-if="form.schedule_id" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
          <div class="lg:col-span-7 space-y-3">
            <div class="flex items-center justify-between px-1">
              <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Live Monitor</h2>
              <span class="text-[10px] font-medium px-2 py-0.5 bg-slate-200 rounded text-slate-600 uppercase">Auto-Refresh</span>
            </div>

            <div v-if="loadingQueue" class="p-12 flex justify-center bg-white rounded-2xl border border-slate-100">
              <div class="animate-spin w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full"></div>
            </div>

            <div v-else class="space-y-2">
              <div v-if="queues.length === 0" class="py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                <i class="fas fa-layer-group text-slate-200 text-3xl mb-2"></i>
                <p class="text-slate-400 text-xs font-medium">Antrean masih kosong</p>
              </div>

              <div v-for="item in queues" :key="item.id" 
                class="group flex items-center gap-4 p-3 rounded-xl border transition-all duration-200"
                :class="item.status === 'playing' ? 'bg-indigo-600 border-indigo-400 shadow-lg shadow-indigo-100 scale-[1.02]' : 'bg-white border-slate-100'">
                
                <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm"
                  :class="item.status === 'playing' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'">
                  #{{ item.queue_number }}
                </div>

                <div class="flex-grow">
                  <h4 class="font-bold text-sm" :class="item.status === 'playing' ? 'text-white' : 'text-slate-800'">
                    {{ item.nickname }}
                  </h4>
                  <p class="text-[10px]" :class="item.status === 'playing' ? 'text-indigo-100' : 'text-slate-400'">
                    ID: {{ item.mlbb_id }} ({{ item.mlbb_server }})
                  </p>
                </div>

                <div class="flex items-center gap-2">
                  <span v-if="item.status === 'playing'" class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                  </span>
                  <span class="text-[10px] font-black uppercase tracking-tighter px-2 py-1 rounded-md"
                    :class="item.status === 'playing' ? 'bg-white text-indigo-600' : 'bg-slate-50 text-slate-400'">
                    {{ item.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <aside class="lg:col-span-5 space-y-4 lg:sticky lg:top-20">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
              <div class="mb-4">
                <h3 class="font-bold text-slate-900">Daftar Antrean</h3>
                <p class="text-xs text-slate-500">Masukkan data MLBB kamu dengan benar.</p>
              </div>

              <div class="space-y-3">
                <div class="flex gap-2">
                  <input v-model="form.mlbb_id" placeholder="User ID" class="w-2/3 px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                  <input v-model="form.mlbb_server" placeholder="Zone" class="w-1/3 px-4 py-2.5 rounded-lg bg-slate-50 border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all" />
                </div>

                <button @click="fetchNickname" :disabled="loadingNickname || !form.mlbb_id"
                  class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition-all disabled:opacity-50">
                  <i v-if="loadingNickname" class="fas fa-spinner animate-spin mr-2"></i>
                  Cek Nickname
                </button>

                <div v-if="form.nickname" class="pt-3 space-y-3 animate-in zoom-in">
                  <div class="px-4 py-3 bg-emerald-50 border border-emerald-100 rounded-lg flex items-center justify-between">
                    <div>
                      <p class="text-[10px] text-emerald-600 font-bold uppercase">Nickname</p>
                      <p class="font-bold text-emerald-700">{{ form.nickname }}</p>
                    </div>
                    <i class="fas fa-check-circle text-emerald-500"></i>
                  </div>

                  <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-center gap-2 mb-2">
                      <div class="flex-grow bg-white h-10 rounded border border-slate-200 flex items-center justify-center overflow-hidden">
                        <img v-if="captchaUrl" :src="captchaUrl" alt="Captcha" class="h-8" />
                      </div>
                      <button @click="loadCaptcha" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-lg hover:text-indigo-600">
                        <i class="fas fa-sync-alt text-xs"></i>
                      </button>
                    </div>
                    <input v-model="captchaInput" placeholder="Input Captcha" maxlength="5"
                      class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 outline-none uppercase text-center font-bold text-sm tracking-widest" />
                  </div>

                  <button @click="handleSubmit" :disabled="submitting || !captchaInput"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-md shadow-indigo-100 uppercase text-xs tracking-wider transition-all active:scale-[0.98] disabled:opacity-50">
                    {{ submitting ? 'Memproses...' : 'Ambil Antrean' }}
                  </button>
                </div>
              </div>
            </div>

            <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100 flex gap-3">
              <i class="fas fa-info-circle text-indigo-400 mt-0.5"></i>
              <p class="text-[11px] text-indigo-800/70 leading-relaxed">
                Pastikan kamu sudah berada di <b>Lobby Game</b> saat giliranmu tiba.
              </p>
            </div>
          </aside>
        </div>

        <div v-else class="py-24 text-center animate-in fade-in">
          <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-slate-100">
            <i class="fas fa-tv text-2xl text-slate-200"></i>
          </div>
          <h3 class="text-lg font-bold text-slate-800">Siap untuk Mabar?</h3>
          <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">
            Pilih streamer favoritmu di atas untuk melihat antrean aktif.
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import Toast from '../../components/Toast.vue';

export default {
  components: { Toast },
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
      // Menggunakan relative path atau absolute sesuai config proxy/env
      this.captchaUrl = `/api/captcha?t=${new Date().getTime()}`;
      this.captchaInput = '';
    },
    async fetchStreamers() {
      try {
        const res = await axios.get('/api/v1/public/streamers');
        this.streamers = res.data.data;
      } catch (e) {
        console.error("Gagal load streamers");
      }
    },
    async fetchSchedules() {
      this.schedules = [];
      this.form.schedule_id = '';
      this.queues = [];
      try {
        const res = await axios.get('/api/v1/public/schedules', { params: { user_id: this.form.user_id } });
        this.schedules = res.data.data;
      } catch (e) {
        this.$refs.toastRef.add('error', 'Gagal', 'Gagal memuat jadwal streamer');
      }
    },
    handleScheduleChange() {
      this.form.nickname = '';
      this.fetchQueue();
    },
    async fetchNickname() {
      if (!this.form.mlbb_id || !this.form.mlbb_server) {
        this.$refs.toastRef.add('info', 'Data Belum Lengkap', 'Isi User ID dan Zone ID terlebih dahulu');
        return;
      }
      this.loadingNickname = true;
      try {
        const res = await axios.get('/api/v1/mlbb-nickname', { 
          params: { id: this.form.mlbb_id, zone: this.form.mlbb_server } 
        });
        if (res.data.success) {
          this.form.nickname = res.data.name;
          this.$refs.toastRef.add('success', 'Akun Ditemukan', `Halo, ${res.data.name}!`);
        } else {
          this.$refs.toastRef.add('error', 'Tidak Ditemukan', 'ID MLBB tidak valid');
        }
      } catch (e) {
        this.$refs.toastRef.add('error', 'Error', 'Terjadi kesalahan pengecekan ID');
      } finally { 
        this.loadingNickname = false; 
      }
    },
    async fetchQueue() {
      if (!this.form.schedule_id) return;
      this.loadingQueue = true;
      try {
        const res = await axios.get('/api/v1/public/queue-list', { params: { schedule_id: this.form.schedule_id } });
        this.queues = res.data.data;
      } finally { 
        this.loadingQueue = false; 
      }
    },
    async handleSubmit() {
      if (!this.captchaInput) {
        this.$refs.toastRef.add('info', 'Captcha', 'Harap isi kode captcha');
        return;
      }
      this.submitting = true;
      try {
        const res = await axios.post('/api/v1/public/join-queue', {
          ...this.form,
          captcha: this.captchaInput
        });
        
        // SUKSES
        this.$refs.toastRef.add(
          'success',
          'Berhasil!',
          `Nomor antrean kamu adalah #${res.data.data.queue_number}`
        );
        
        // RESET FORM
        this.form.nickname = '';
        this.form.mlbb_id = '';
        this.form.mlbb_server = '';
        this.captchaInput = '';
        this.loadCaptcha();
        this.fetchQueue();
      } catch (e) {
        const msg = e.response?.data?.message || "Gagal mendaftar antrean";
        this.$refs.toastRef.add('error', 'Gagal', msg);
        this.loadCaptcha();
      } finally { 
        this.submitting = false; 
      }
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