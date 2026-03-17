<template>
  <div class="fixed inset-0 w-full flex flex-col md:flex-row-reverse bg-white font-sans overflow-hidden">

    <div class="hidden md:flex md:w-1/2 bg-[#1e60ff] relative overflow-y-auto">
      <img class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-20" />

      <div
        class="relative z-10 p-10 lg:p-14 flex flex-col justify-between text-white w-full h-full text-right items-end">
        <div class="flex items-center gap-4 flex-row-reverse">
          <div class="bg-white/10 p-2 rounded-xl backdrop-blur-sm border border-white/20">
            <!-- <img :src="Background" class="w-8 h-8 brightness-0 invert" /> -->
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

    <div class="w-full md:w-1/2 flex items-center justify-center bg-white p-6 md:p-10 lg:p-14 py-12">
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
              <input v-model="name" type="text" placeholder="Masukkan nama anda" required class="input-style" />
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
            <div
              class="flex items-center justify-between mb-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">

              <div class="flex-grow flex justify-center">
                <img v-if="captchaUrl" :src="captchaUrl" alt="Captcha"
                  class="h-10 rounded select-none pointer-events-none" />
              </div>

              <button @click="loadCaptcha" type="button"
                class="text-blue-600 p-2 hover:bg-blue-50 rounded-full transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </button>
            </div>

            <input v-model="captchaInput" placeholder="KODE CAPTCHA" maxlength="5" required
              class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-600 outline-none uppercase text-center tracking-widest font-bold text-sm" />
          </div>

          <button type="submit" :disabled="isLoading"
            class="w-full bg-[#4f93af] hover:bg-[#3d7a92] text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-[0.99] cursor-pointer disabled:opacity-50">
            {{ isLoading ? 'Mendaftarkan...' : 'DAFTAR SEKARANG' }}
          </button>

        </form>

        <p class="mt-6 text-center text-xs md:text-sm text-gray-500 font-medium">
          Sudah punya akun?
          <router-link to="/login" class="text-blue-600 font-bold hover:underline">
            Masuk di sini
          </router-link>
        </p>

        <transition name="fade">
          <div v-if="errorMessage || validationErrors.length"
            class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm animate-shake">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
              </div>
              <div class="ml-3">
                <h3 class="text-xs font-black text-red-800 uppercase tracking-wider">Terjadi Kesalahan</h3>
                <div class="mt-1 text-xs text-red-700 font-medium">
                  <p v-if="errorMessage && !validationErrors.length">{{ errorMessage }}</p>

                  <ul v-else class="list-disc pl-4 space-y-1">
                    <li v-for="(err, index) in validationErrors" :key="index">{{ err }}</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </transition>

      </div>
    </div>

  </div>
</template>

<script>
import axios from "axios"

export default {
  data() {
    return {
      name: "",
      email: "",
      password: "",
      channel_name: "",
      captchaUrl: "",
      captchaInput: "",
      validationErrors: [],
      errorMessage: "",
      isLoading: false,
    }
  },

  mounted() {
    this.loadCaptcha()
  },

  methods: {
    loadCaptcha() {
      this.captchaUrl = `api/captcha?t=${new Date().getTime()}`
      this.captchaInput = ""
    },

    async register() {
      if (!this.email || !this.password || !this.captchaInput) {
        this.errorMessage = "Semua field bertanda bintang dan captcha wajib diisi."
        return
      }

      this.isLoading = true
      this.errorMessage = ""
      this.validationErrors = [];

      try {
        await axios.post(`api/v1/register`, {
          name: this.name,
          email: this.email,
          password: this.password,
          channel_name: this.channel_name,
          captcha: this.captchaInput
        })
        this.$router.push("/login")
      } catch (err) {
        const response = err.response;

        if (response?.status === 422) {
          const errors = response.data.errors;
          this.validationErrors = Object.values(errors).flat();
        } else {
          this.errorMessage = response?.data?.message || "Registrasi gagal, silakan coba lagi.";
        }

        this.loadCaptcha();
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
@reference 'tailwindcss';

.input-style {
  @apply w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all text-sm;
}

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