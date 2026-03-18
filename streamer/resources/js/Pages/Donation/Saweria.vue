<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'saweria'" />
    <Toast ref="toastRef" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header
        class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="p-2 bg-orange-500 rounded-lg shadow-orange-200 shadow-lg">
            <i class="fas fa-plug text-white"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Integrasi Saweria</h1>
            <p class="text-xs text-gray-500 font-medium">Hubungkan donasi untuk sistem pencatatan donasi otomatis</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Connection Status</span>
          <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
        </div>
      </header>

      <div class="p-6 space-y-6">
        <section class="bg-gray-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
          <div class="absolute right-0 top-0 opacity-10 translate-x-1/4 -translate-y-1/4">
            <i class="fas fa-link text-[120px]"></i>
          </div>

          <div class="relative z-10 max-w-2xl">
            <h2 class="text-lg font-bold mb-2">URL Webhook Anda</h2>
            <p class="text-gray-400 text-xs mb-6 leading-relaxed">
              Salin URL di bawah ini dan tempelkan ke bagian <b>Webhook</b> di dashboard Saweria Anda.
              Sistem akan otomatis mencatat donasi yang masuk ke akun Anda.
            </p>

            <div class="flex items-center gap-2 bg-white/10 p-1.5 rounded-xl border border-white/10 backdrop-blur-sm">
              <code class="flex-1 px-3 text-sm text-blue-300 font-mono truncate">
                {{ webhookUrl }}
              </code>
              <button @click="copyWebhook"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-xs font-bold transition-all active:scale-95 flex items-center gap-2">
                <i class="fas fa-copy"></i>
                Copy URL
              </button>
            </div>
          </div>
        </section>

        <div v-if="loading" class="flex items-center justify-center min-h-[300px]">
          <div class="animate-spin w-10 h-10 border-4 border-orange-500 border-t-transparent rounded-full"></div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
              <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-tighter">Log Webhook Terbaru</h2>
                <div class="relative">
                  <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                  <input type="text" v-model="searchQuery" placeholder="Cari donatur..."
                    class="pl-9 pr-4 py-1.5 bg-gray-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-orange-500 w-48 lg:w-64 transition-all" />
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Donatur</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Pesan</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Jumlah</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase text-center">Waktu</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="donation in paginatedDonations" :key="donation.id"
                      class="hover:bg-gray-50/50 transition-colors">
                      <td class="px-4 py-4">
                        <div class="flex flex-col">
                          <span class="text-xs font-bold text-gray-800">{{ donation.donator_name }}</span>
                          <span class="text-[9px] text-gray-400">Ref: {{ donation.external_id || '-' }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-4">
                        <p class="text-[11px] text-gray-600 italic line-clamp-1 max-w-[150px]">
                          "{{ donation.message || 'No Message' }}"
                        </p>
                      </td>
                      <td class="px-4 py-4">
                        <span class="text-xs font-black text-gray-900">{{ formatPrice(donation.amount) }}</span>
                      </td>
                      <td class="px-4 py-4 text-center">
                        <span class="text-[10px] text-gray-400 font-medium uppercase">{{ formatTime(donation.created_at)
                          }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="filteredDonations.length === 0" class="p-12 text-center">
                  <p class="text-gray-400 text-xs italic">Belum ada donasi terdeteksi.</p>
                </div>
              </div>

              <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between mt-auto">
                <button @click="currentPage--" :disabled="currentPage === 1" class="p-2 disabled:opacity-30"><i
                    class="fas fa-chevron-left text-xs"></i></button>
                <span class="text-[10px] font-bold text-gray-400 uppercase">Hal {{ currentPage }} / {{ totalPages
                  }}</span>
                <button @click="currentPage++" :disabled="currentPage === totalPages" class="p-2 disabled:opacity-30"><i
                    class="fas fa-chevron-right text-xs"></i></button>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
              <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Cara Integrasi</h3>
              <ul class="space-y-4">
                <li class="flex gap-3">
                  <div
                    class="w-5 h-5 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                    1</div>
                  <p class="text-[11px] text-gray-600 leading-relaxed">Buka <a
                      href="https://saweria.co/admin/integrations" target="_blank"
                      class="text-blue-600 font-bold underline">Integration Saweria</a>.</p>
                </li>
                <li class="flex gap-3">
                  <div
                    class="w-5 h-5 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                    2</div>
                  <p class="text-[11px] text-gray-600 leading-relaxed">Klik tombol <b>Edit Webhook</b>.</p>
                </li>
                <li class="flex gap-3">
                  <div
                    class="w-5 h-5 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                    3</div>
                  <p class="text-[11px] text-gray-600 leading-relaxed">Paste URL Webhook Anda di kolom Endpoint URL dan
                    Simpan.</p>
                </li>
              </ul>
            </div>

            <div class="bg-blue-600 p-6 rounded-2xl text-white shadow-lg relative overflow-hidden group">
              <i
                class="fas fa-crown absolute -right-2 -bottom-2 text-6xl opacity-10 group-hover:scale-110 transition-transform"></i>
              <h3 class="text-xs font-bold text-blue-200 uppercase tracking-widest mb-2">Automasi VIP</h3>
              <p class="text-[11px] leading-relaxed text-blue-50/80 mb-3">
                Sistem akan otomatis menambah <b>Play Balance</b> jika donatur menyertakan kata <span
                  class="bg-white/20 px-1 rounded text-white font-mono">VIP</span> dan format ID di pesan Saweria.
              </p>
              <div class="bg-black/20 p-2 rounded-lg border border-white/10">
                <p class="text-[9px] text-blue-200 uppercase font-bold mb-1">Contoh Pesan:</p>
                <p class="text-[10px] font-mono text-white italic">"VIP 12345678 (1234)"</p>
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
import Toast from '@/components/Toast.vue';
import axios from 'axios';

export default {
  components: { Sidebar, Toast },
  data() {
    return {
      user: null,
      donations: [],
      loading: true,
      currentPage: 1,
      itemsPerPage: 8,
      searchQuery: ''
    }
  },
  computed: {
    webhookUrl() {
      if (!this.user?.webhook_token) return 'Loading...';
      return `https://koryuz.com/api/v1/webhook/saweria/${this.user.webhook_token}`;
    },
    // PERBAIKAN: Hanya mengambil data yang platformnya saweria
    filteredDonations() {
      const list = this.donations.filter(d => d.platform === 'saweria');
      if (!this.searchQuery) return list;
      return list.filter(d =>
        d.donator_name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    totalPages() {
      return Math.ceil(this.filteredDonations.length / this.itemsPerPage) || 1;
    },
    paginatedDonations() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredDonations.slice(start, end);
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const userRes = await axios.get('/api/v1/me');
        this.user = userRes.data;

        const donRes = await axios.get('/api/v1/donations');
        // Sorting tetap ada agar data terbaru di atas
        this.donations = donRes.data.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } catch (error) {
        console.error("Error loading data:", error);
      } finally {
        this.loading = false;
      }
    },
    copyWebhook() {
      navigator.clipboard.writeText(this.webhookUrl);
      this.$refs.toastRef.add('success', 'Tersalin!', 'URL Webhook berhasil dicopy ke clipboard.');
    },
    formatPrice(value) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(value);
    },
    formatTime(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
    }
  }
}
</script>