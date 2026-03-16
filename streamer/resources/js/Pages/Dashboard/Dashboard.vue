<template>
  <div class="flex h-screen bg-[#f8fafc] overflow-hidden font-sans text-slate-900">
    <Sidebar :activeMenu="'dashboard'" />

    <main class="flex-1 min-w-0 flex flex-col overflow-hidden relative">
      <div class="absolute top-0 right-0 w-[40%] h-[30%] bg-blue-500/5 blur-[120px] rounded-full -z-10"></div>
      
      <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0 z-10">
        <div>
          <h1 class="text-sm font-bold tracking-tight text-slate-800 flex items-center gap-2">
            <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
            STREAM <span class="text-blue-600 uppercase">Command</span>
          </h1>
          <p class="text-[10px] text-slate-400 font-bold tracking-widest uppercase mt-0.5">Control Center // {{ currentDate }}</p>
        </div>

        <div class="flex items-center gap-6">
          <div class="hidden sm:flex items-center gap-4 border-r border-slate-200 pr-6">
            <div class="text-right">
              <span class="block text-[9px] font-bold text-slate-400 uppercase leading-none">Signal</span>
              <span class="text-xs font-mono font-bold transition-colors duration-300" :class="latencyColorClass">
                {{ latency }}<span class="text-[10px] ml-0.5">ms</span>
              </span>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <button class="p-2 text-slate-400 hover:text-blue-600 transition-colors relative">
              <i class="fas fa-bell text-sm"></i>
              <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-red-500 rounded-full border border-white"></span>
            </button>
            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
              <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="User">
            </div>
          </div>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-6 custom-scrollbar z-10">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></span>
                  <span class="text-[10px] font-bold uppercase tracking-wider">Upcoming Session</span>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mt-4 leading-tight">
                  {{ schedules[0]?.title || 'System Standby' }}
                </h2>
              </div>
              
              <div class="flex flex-wrap items-center gap-3 mt-6">
                <a href="https://studio.youtube.com/" target="_blank" 
                   class="bg-slate-900 text-white text-[11px] font-bold px-5 py-2.5 rounded-lg hover:bg-blue-600 transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
                  <i class="fas fa-video text-[10px]"></i> START BROADCAST
                </a>
                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                  <i class="fas fa-clock text-blue-500 text-[10px]"></i>
                  <span class="text-[11px] font-bold text-slate-600">{{ formatTime(schedules[0]?.start_time) }}</span>
                </div>
              </div>
            </div>
            <i class="fas fa-bolt absolute -right-6 -bottom-6 text-slate-50 text-9xl group-hover:text-blue-50/50 transition-colors duration-500"></i>
          </div>

          <div class="bg-slate-900 rounded-xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-center">
            <h3 class="text-white/40 text-[10px] font-bold uppercase tracking-[0.2em] mb-4">Quick Shortcuts</h3>
            <div class="space-y-2">
              <router-link to="/schedules/create" class="flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 transition-all group">
                <span class="text-xs font-semibold text-white">Schedule Stream</span>
                <i class="fas fa-plus-circle text-blue-400 text-xs group-hover:rotate-90 transition-transform"></i>
              </router-link>
              <router-link to="/players/create" class="flex items-center justify-between p-3 bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 transition-all group">
                <span class="text-xs font-semibold text-white">Add Teammate</span>
                <i class="fas fa-user-plus text-purple-400 text-xs group-hover:scale-110 transition-transform"></i>
              </router-link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-widest">Broadcast Timeline</h2>
                <div class="flex gap-1">
                  <button @click="fetchSchedules(pagination.prev_page_url)" class="p-1.5 rounded-md hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                  </button>
                  <button @click="fetchSchedules(pagination.next_page_url)" class="p-1.5 rounded-md hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                  </button>
                </div>
              </div>

              <div class="p-2">
                <div v-for="item in schedules" :key="item.id"
                  class="group flex items-center gap-4 p-3 rounded-lg border border-transparent hover:border-slate-100 hover:bg-slate-50 transition-all">
                  
                  <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 flex flex-col items-center justify-center group-hover:bg-blue-600 group-hover:border-blue-600 transition-all shrink-0">
                    <span class="text-[8px] font-bold text-slate-400 group-hover:text-blue-100 uppercase">{{ formatDay(item.start_time) }}</span>
                    <span class="text-sm font-bold text-slate-700 group-hover:text-white leading-none">{{ formatDate(item.start_time) }}</span>
                  </div>

                  <div class="flex-grow min-w-0">
                    <h4 class="text-xs font-bold text-slate-700 truncate uppercase tracking-tight">{{ item.title || 'Broadcast Session' }}</h4>
                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-[10px] font-medium text-slate-400">{{ formatTime(item.start_time) }}</span>
                      <span :class="statusBadgeClass(item.status)" class="text-[8px] px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">
                        {{ item.status }}
                      </span>
                    </div>
                  </div>

                  <router-link :to="`/schedules/${item.id}`"
                    class="opacity-0 group-hover:opacity-100 p-2 rounded-md bg-slate-900 text-white transition-all shadow-md">
                    <i class="fas fa-cog text-[10px]"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200">
              <h2 class="text-[10px] font-bold text-slate-400 mb-6 uppercase tracking-[0.2em] flex items-center justify-between">
                <span>Stream Performance</span>
                <span class="flex h-2 w-2 relative">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
              </h2>

              <div class="space-y-5">
                <div>
                  <div class="flex justify-between text-[10px] mb-2 font-bold uppercase">
                    <span class="text-slate-500">Video Bitrate</span>
                    <span class="text-blue-600 font-mono">6.4 Mbps</span>
                  </div>
                  <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: 75%"></div>
                  </div>
                </div>

                <div>
                  <div class="flex justify-between text-[10px] mb-2 font-bold uppercase">
                    <span class="text-slate-500">Dropped Frames</span>
                    <span class="text-emerald-500 font-mono">0.0%</span>
                  </div>
                  <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 2%"></div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                  <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[9px] font-bold text-slate-400 uppercase">Viewers</p>
                    <p class="text-lg font-bold text-slate-800 font-mono leading-none mt-1">1.2k</p>
                  </div>
                  <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[9px] font-bold text-slate-400 uppercase">Uptime</p>
                    <p class="text-lg font-bold text-slate-800 font-mono leading-none mt-1">02h:45m</p>
                  </div>
                </div>
              </div>

              <button class="w-full mt-8 py-3 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all border border-slate-100 cursor-pointer">
                Full Analytics <i class="fas fa-arrow-right ml-1"></i>
              </button>
            </div>

            <div class="bg-slate-900 rounded-xl p-5 border border-slate-800">
              <h3 class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-3">Live Console</h3>
              <div class="space-y-1.5 font-mono text-[9px]">
                <p class="text-emerald-400/80"><span class="text-white/20">14:20:01</span> [INFO] Connection stabilized</p>
                <p class="text-blue-400/80"><span class="text-white/20">14:19:45</span> [CMD] Encoder settings updated</p>
                <p class="text-white/50"><span class="text-white/20">14:18:20</span> [SYS] Monitoring active...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue'
import axios from 'axios'

export default {
  components: { Sidebar },

  data() {
    return {
      activeMenu: "dashboard",
      loading: true,
      currentDate: new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long'
      }).format(new Date()),

      schedules: [],
      latency: 0,
      latencyInterval: null,

      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        next_page_url: null,
        prev_page_url: null
      }
    }
  },

  computed: {
    latencyColorClass() {
      if (this.latency === 0) return 'text-slate-400 animate-pulse';
      if (this.latency < 100) return 'text-emerald-500';
      if (this.latency < 250) return 'text-amber-500';
      return 'text-rose-500';
    }
  },

  mounted() {
    this.initializeDashboard()
    this.checkLatency()
    this.latencyInterval = setInterval(this.checkLatency, 10000)
  },
  
  beforeUnmount() {
    if (this.latencyInterval) {
      clearInterval(this.latencyInterval)
    }
  },

  methods: {
    async checkLatency() {
      const startTime = performance.now();
      try {
        await axios.head(window.location.origin, { 
          timeout: 5000, 
          headers: { 'Cache-Control': 'no-cache' } 
        });
        this.latency = Math.round(performance.now() - startTime);
      } catch (error) {
        this.latency = 999;
      }
    },

    async initializeDashboard() {
      const token = localStorage.getItem("token")
      if (!token) {
        this.$router.push("/login")
        return
      }
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      await this.fetchSchedules()
    },

    async fetchSchedules(url = '/api/v1/schedules') {
      if (!url) return;
      this.loading = true
      try {
        const response = await axios.get(url)
        const result = response?.data?.data
        if (result) {
          this.schedules = result.data || []
          this.pagination = {
            current_page: result.current_page,
            last_page: result.last_page,
            per_page: result.per_page,
            total: result.total,
            next_page_url: result.next_page_url,
            prev_page_url: result.prev_page_url
          }
        }
      } catch (error) {
        if (error.response?.status === 401) {
          localStorage.removeItem("token")
          this.$router.push("/login")
        }
      } finally {
        this.loading = false
      }
    },

    // --- HELPER FORMATTING (Fixed) ---
    formatDay(dateStr) {
      if (!dateStr) return '---';
      const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB']
      return days[new Date(dateStr).getDay()]
    },

    formatDate(dateStr) {
      if (!dateStr) return '--';
      return new Date(dateStr).getDate().toString().padStart(2, '0')
    },

    formatTime(dateStr) {
      if (!dateStr) return '--:-- WIB';
      return new Date(dateStr).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
      }) + ' WIB'
    },

    statusBadgeClass(status) {
      switch (status?.toLowerCase()) {
        case 'live':
          return 'bg-rose-50 text-rose-600 border-rose-100';
        case 'scheduled':
          return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'finished':
          return 'bg-slate-50 text-slate-400 border-slate-200';
        default:
          return 'bg-amber-50 text-amber-600 border-amber-100';
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
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
</style>