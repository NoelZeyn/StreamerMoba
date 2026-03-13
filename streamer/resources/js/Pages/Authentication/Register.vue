<template>
  <div class="fixed inset-0 w-full flex flex-col md:flex-row-reverse bg-white font-sans overflow-hidden">

    <div class="hidden md:flex md:w-1/2 bg-[#1e60ff] relative overflow-hidden">
      <img :src="Background" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-20" />

      <div
        class="relative z-10 p-10 lg:p-14 flex flex-col justify-between text-white w-full h-full text-right items-end">
        <div class="flex items-center gap-4 flex-row-reverse">
          <div class="bg-white/10 p-2 rounded-xl backdrop-blur-sm border border-white/20">
            <img :src="Background" class="w-8 h-8 brightness-0 invert" />
          </div>
          <span class="text-xl font-bold tracking-wider uppercase">Sistem Stream</span>
        </div>

        <div class="max-w-md">
          <h2 class="text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
            Mulai Perjalanan <br />
            <span class="opacity-80">Bersama Kami.</span>
          </h2>
          <p class="text-blue-100 text-lg leading-relaxed opacity-90">
            Daftarkan akun anda untuk mulai mengelola stream secara terstruktur dan efisien.
          </p>
        </div>

        <p class="text-sm text-blue-200 font-medium tracking-widest">
          © 2026 Vasta Technology
        </p>
      </div>

      <div
        class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply blur-3xl opacity-20">
      </div>
    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center bg-white p-6 md:p-10 lg:p-14">
      <div class="w-full max-w-md animate-slide-in">

        <div class="mb-8 text-center md:text-left">
          <h3 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
            Daftar Akun
          </h3>
          <p class="text-gray-500 text-sm md:text-base">
            Lengkapi data di bawah untuk bergabung
          </p>
        </div>

        <form @submit.prevent="register" class="space-y-3">

          <div class="grid grid-cols-1 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">
                Nama Lengkap <span class="text-red-500">*</span>
              </label>
              <input v-model="name" type="text" placeholder="Masukkan nama anda" class="input-style" />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">
                Alamat Email <span class="text-red-500">*</span>
              </label>
              <input v-model="email" type="email" placeholder="nama@email.com" required class="input-style" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">
              Password <span class="text-red-500">*</span>
            </label>
            <input v-model="password" type="password" placeholder="••••••••" required class="input-style" />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wide">
              Nama Channel
            </label>
            <input v-model="channel_name" type="text" placeholder="Contoh: Stream-01" class="input-style" />
          </div>

          <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
            <div class="flex items-center justify-between mb-2 bg-white p-2 rounded-lg border border-gray-100">
              <div v-html="captchaImage" class="flex-grow flex justify-center scale-90"></div>

              <button @click="loadCaptcha" type="button" class="text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11
                           11v-5h-.581m0 0a8.003 8.003 0
                           01-15.357-2m15.357 2H15" />
                </svg>
              </button>
            </div>

            <input v-model="captchaInput" placeholder="KODE CAPTCHA"
              class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-600 outline-none uppercase text-center tracking-widest font-bold text-sm" />
          </div>

          <button type="submit"
            class="w-full bg-[#4f93af] hover:bg-[#3d7a92] text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-[0.99] cursor-pointer">
            DAFTAR SEKARANG
          </button>

        </form>

        <p class="mt-6 text-center text-xs md:text-sm text-gray-500 font-medium">
          Sudah punya akun?
          <router-link to="/login" class="text-blue-600 font-bold hover:underline">
            Masuk di sini
          </router-link>
        </p>

        <transition name="fade">
          <div v-if="errorMessage"
            class="mt-4 p-3 bg-red-50 text-red-600 rounded-lg text-xs text-center font-bold border border-red-100">
            {{ errorMessage }}
          </div>
        </transition>

      </div>
    </div>

  </div>
</template>

<script>
import Background from "@/assets/PLN.svg"
import axios from "axios"

export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      channel_name: "",
      captchaImage: "",
      captchaInput: "",
      errorMessage: "",
      Background,
    }
  },

  mounted() {
    this.loadCaptcha()
  },

  methods: {
    async loadCaptcha() {
      try {
        const res = await axios.get("/api/captcha")
        this.captchaImage = res.data.captcha
      } catch (e) {
        console.error(e)
      }
    },

    async register() {
      if (!this.email || !this.password) {
        this.errorMessage = "Email dan password wajib diisi."
        return
      }

      try {
        await axios.post("/api/v1/register", {
          name: this.name,
          email: this.email,
          password: this.password,
          channel_name: this.channel_name,
          captcha: this.captchaInput
        })

        this.$router.push("/login")
      } catch (err) {
        this.errorMessage = err.response?.data?.message || "Terjadi kesalahan."
        this.loadCaptcha()
      }
    }
  }
}
</script>

<style scoped>
@reference "tailwindcss";

.input-style {
  @apply w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all text-sm;
}

/* slide animation */
.animate-slide-in {
  animation: slideIn 0.6s ease-out forwards;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }

  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>