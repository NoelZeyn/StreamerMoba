<template>
  <div class="flex h-screen bg-gray-50/50 overflow-hidden font-sans">
    <Sidebar :activeMenu="'donations'" />

    <main class="flex-1 min-w-0 overflow-y-auto custom-scrollbar flex flex-col">
      <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="p-2 bg-blue-600 rounded-lg shadow-blue-200 shadow-lg">
            <i class="fas fa-hand-holding-heart text-white"></i>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-900">Donasi Masuk</h1>
            <p class="text-xs text-gray-500 font-medium">Log Webhook Saweria & Manual</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Real-time update</span>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
        </div>
      </header>

      <div class="p-6">
        <div v-if="loading" class="flex items-center justify-center min-h-[400px]">
          <div class="animate-spin w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full"></div>
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
              <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-tighter">Riwayat Transaksi</h2>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" v-model="searchQuery" placeholder="Cari donatur..." 
                        class="pl-9 pr-4 py-1.5 bg-gray-50 border-none rounded-lg text-xs focus:ring-2 focus:ring-blue-500 w-64 transition-all" />
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                  <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase w-12 text-center">No</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Donatur</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Pesan</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Jumlah</th>
                      <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase text-center">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <tr v-for="(donation, index) in paginatedDonations" :key="donation.id" class="hover:bg-gray-50/50 transition-colors">
                      <td class="px-4 py-6 text-xs text-center font-medium text-gray-400">
                        {{ ((currentPage - 1) * itemsPerPage) + index + 1 }}
                      </td>
                      <td class="px-4 py-6">
                        <div class="flex flex-col">
                          <span class="text-sm font-bold text-gray-800">{{ donation.donator_name }}</span>
                          <span class="text-[10px] text-gray-400">{{ donation.email || 'tanpa email' }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-6">
                        <p class="text-xs text-gray-600 italic line-clamp-2 max-w-xs">
                          "{{ donation.message || 'Tidak ada pesan' }}"
                        </p>
                        <span v-if="donation.saweria_id" class="mt-1 inline-block text-[9px] font-bold bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded">
                           ID: {{ donation.saweria_id }}
                        </span>
                      </td>
                      <td class="px-4 py-6">
                        <div class="flex flex-col">
                          <span class="text-sm font-black text-gray-900">{{ formatPrice(donation.amount) }}</span>
                          <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ donation.currency }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-6 text-center">
                         <div class="flex flex-col items-center gap-1">
                             <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase">Success</span>
                             <span class="text-[9px] text-gray-400 font-medium">{{ formatTime(donation.created_at) }}</span>
                         </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div v-if="filteredDonations.length === 0" class="p-16 text-center bg-white">
                  <i class="fas fa-box-open text-gray-200 text-4xl mb-4"></i>
                  <p class="text-gray-400 italic text-sm">Belum ada data donasi masuk.</p>
                </div>
              </div>

              <div class="p-4 bg-white border-t border-gray-100 flex items-center justify-between">
                <button @click="currentPage--" :disabled="currentPage === 1"
                  class="px-4 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg disabled:opacity-50 hover:bg-gray-200 transition-all">
                  Prev
                </button>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                  Page {{ currentPage }} of {{ totalPages }}
                </span>
                <button @click="currentPage++" :disabled="currentPage === totalPages"
                  class="px-4 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg disabled:opacity-50 hover:bg-gray-200 transition-all">
                  Next
                </button>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden">
              <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/20 rounded-full blur-2xl"></div>
              <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6">Revenue Summary</h3>
              <div class="space-y-4">
                <div class="flex justify-between items-end border-b border-white/10 pb-2">
                  <span class="text-xs text-gray-400">Total Saldo Masuk</span>
                  <span class="text-2xl font-black">{{ formatPrice(totalRevenue) }}</span>
                </div>
                <div class="flex justify-between items-end border-b border-white/10 pb-2">
                  <span class="text-xs text-gray-400">Total Donatur</span>
                  <span class="text-2xl font-black text-blue-400">{{ donations.length }}</span>
                </div>
              </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Donatur Terakhir</h3>
                <div v-if="donations.length > 0" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-black">
                        {{ donations[0].donator_name.charAt(0) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ donations[0].donator_name }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold uppercase">{{ formatPrice(donations[0].amount) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 p-6 rounded-2xl text-white shadow-xl relative overflow-hidden">
              <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6">Shortcuts</h3>
              <div class="grid grid-cols-2 gap-3">
                <button @click="refreshData" class="bg-white/5 hover:bg-white/10 p-4 rounded-xl text-center border border-white/10 transition-all group">
                  <i class="fas fa-sync-alt mb-2 block text-blue-400 group-hover:rotate-180 transition-transform duration-500"></i>
                  <p class="text-[9px] font-bold uppercase tracking-widest text-blue-300">Refresh</p>
                </button>
                <a href="https://saweria.co" target="_blank" class="bg-white/5 hover:bg-orange-500/20 p-4 rounded-xl text-center border border-white/10 transition-all group">
                  <i class="fas fa-external-link-alt mb-2 block text-orange-400 group-hover:scale-110 transition"></i>
                  <p class="text-[9px] font-bold uppercase tracking-widest text-orange-300">Saweria</p>
                </a>
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
      donations: [],
      loading: true,
      currentPage: 1,
      itemsPerPage: 10,
      searchQuery: ''
    }
  },
  computed: {
    filteredDonations() {
      if (!this.searchQuery) return this.donations;
      return this.donations.filter(d => 
        d.donator_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        (d.message && d.message.toLowerCase().includes(this.searchQuery.toLowerCase()))
      );
    },
    totalPages() {
      return Math.ceil(this.filteredDonations.length / this.itemsPerPage);
    },
    paginatedDonations() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredDonations.slice(start, end);
    },
    totalRevenue() {
      return this.donations.reduce((acc, d) => acc + parseInt(d.amount), 0);
    }
  },
  mounted() {
    this.fetchDonations();
  },
  methods: {
    async fetchDonations() {
      this.loading = true;
      try {
        const response = await axios.get('/api/v1/donations');
        this.donations = response.data.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } catch (error) {
        console.error("Error loading donations:", error);
      } finally {
        this.loading = false;
      }
    },
    refreshData() {
        this.fetchDonations();
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>