<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'schedules'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-full transition-all active:scale-95">
            <i class="fas fa-arrow-left text-gray-600"></i>
          </button>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Buat Jadwal Baru</h1>
            <p class="text-xs text-gray-500 font-medium italic">Tambahkan sesi streaming ke dalam sistem</p>
          </div>
        </div>
      </header>

      <div class="p-6">
        <div class="max-w-4xl mx-auto">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
              <form @submit.prevent="handleSubmit" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-widest italic border-l-4 border-blue-500 pl-3">
                  Informasi Utama
                </h2>

                <div class="space-y-5">
                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Judul Sesi Streaming</label>
                    <input 
                      v-model="form.title" 
                      type="text" 
                      placeholder="Contoh: Push Rank Malam Minggu"
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium"
                      required
                    />
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                      <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Waktu Mulai</label>
                      <input 
                        v-model="form.start_time" 
                        type="datetime-local" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium"
                        required
                      />
                    </div>

                    <div>
                      <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Status Awal</label>
                      <select 
                        v-model="form.status"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium appearance-none"
                      >
                        <option value="scheduled">SCHEDULED</option>
                        <option value="live">LIVE NOW</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-2 block">Catatan Internal (Opsional)</label>
                    <textarea 
                      v-model="form.notes" 
                      rows="3"
                      placeholder="Tambahkan detail atau objektif stream..."
                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-medium"
                    ></textarea>
                  </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-100 flex gap-3">
                  <button 
                    type="submit" 
                    :disabled="submitting"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 active:scale-[0.98] disabled:opacity-50"
                  >
                    <i v-if="submitting" class="fas fa-circle-notch animate-spin"></i>
                    {{ submitting ? 'MENYIMPAN...' : 'SIMPAN JADWAL' }}
                  </button>
                  <button 
                    type="button"
                    @click="$router.back()"
                    class="px-6 py-3 rounded-xl border border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition-all"
                  >
                    BATAL
                  </button>
                </div>
              </form>
            </div>

            <div class="space-y-6">
              <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                  <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-4">Panduan Cepat</h3>
                  <ul class="space-y-4">
                    <li class="flex gap-3">
                      <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                      <p class="text-[11px] text-gray-300 leading-relaxed">
                        Jadwal yang dibuat akan muncul di dashboard utama dan dapat diakses oleh streamer.
                      </p>
                    </li>
                    <li class="flex gap-3">
                      <i class="fas fa-clock text-orange-400 mt-1"></i>
                      <p class="text-[11px] text-gray-300 leading-relaxed">
                        Pastikan waktu mulai tidak bentrok dengan jadwal streaming aktif lainnya.
                      </p>
                    </li>
                  </ul>
                </div>
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
              </div>

              <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                <h3 class="text-[10px] font-black text-blue-600 uppercase mb-2">Live Preview Card</h3>
                <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-sm">
                   <p class="text-[9px] font-bold text-blue-600 uppercase mb-1">{{ form.start_time ? formatDatePreview(form.start_time) : 'Waktu Belum Diset' }}</p>
                   <h4 class="text-xs font-bold text-gray-800 uppercase truncate">{{ form.title || 'Judul Stream' }}</h4>
                   <div class="flex items-center gap-2 mt-2">
                     <span class="text-[9px] font-black uppercase text-blue-500 bg-blue-50 px-2 py-0.5 rounded">{{ form.status }}</span>
                   </div>
                </div>
              </div>
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

export default {
  components: { Sidebar },
  data() {
    return {
      submitting: false,
      form: {
        title: '',
        start_time: '',
        status: 'scheduled',
        notes: ''
      }
    }
  },
  methods: {
    async handleSubmit() {
      this.submitting = true;
      try {
        await axios.post('/api/v1/schedules', this.form);
        // Toast success notification bisa ditambah di sini
        this.$router.push('/dashboard'); 
      } catch (error) {
        console.error("Gagal menyimpan jadwal:", error);
        alert("Terjadi kesalahan saat menyimpan data.");
      } finally {
        this.submitting = false;
      }
    },
    formatDatePreview(dateStr) {
      try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
      } catch (e) {
        return '';
      }
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

/* Menghilangkan icon default kalender di beberapa browser untuk estetika */
input[type="datetime-local"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
  opacity: 0.6;
  filter: invert(0.5);
}
</style>