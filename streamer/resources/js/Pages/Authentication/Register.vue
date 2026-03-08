<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-xl w-full max-w-md">

      <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-800">Selamat Datang</h2>
        <p class="text-gray-500 mt-2">Silakan login ke akun Anda</p>
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email<span class="text-red-500">*</span></label>
          <input v-model="email" type="email" placeholder="nama@email.com"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
          <input v-model="name" type="text" placeholder="Nama Anda"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password<span
              class="text-red-500">*</span></label>
          <input v-model="password" type="password" placeholder="••••••••"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Channel</label>
          <input v-model="channel_name" type="text" placeholder="Nama Channel Anda"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
          <label class="block text-sm font-medium text-gray-700 mb-2">Verifikasi Keamanan</label>

          <div class="flex items-center justify-between bg-white p-2 rounded-lg border mb-3">
            <div v-html="captchaImage"
              class="overflow-hidden flex-grow flex justify-center items-center h-12 grayscale hover:grayscale-0 transition">
            </div>

            <button @click="loadCaptcha" class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
              title="Ganti Captcha">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>

          <input v-model="captchaInput" placeholder="Ketik kode di atas"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none uppercase text-center tracking-widest font-bold" />
        </div>

        <button @click="register"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transform active:scale-[0.98] transition-all">
          Register Sekarang
        </button>

        <transition name="fade">
          <div v-if="errorMessage"
            class="bg-red-50 text-red-600 p-3 rounded-lg text-sm text-center border border-red-100">
            {{ errorMessage }}
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
      captchaImage: "",
      captchaInput: "",
      errorMessage: ""
    }
  },

  mounted() {
    this.loadCaptcha()
  },

  methods: {
    async loadCaptcha() {
      const res = await axios.get("/api/captcha")
      this.captchaImage = res.data.captcha
    },
    async validate() {
      if (!this.email || !this.password) {
        this.errorMessage = "Email dan password wajib diisi."
        return false
      }
      if (!this.captchaInput) {
        this.errorMessage = "Kode captcha wajib diisi."
        return false
      }
      return true
    },
    async register() {
      if (!(await this.validate())) {
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
        window.location.href = "/dashboard"
      } catch (err) {
        this.errorMessage = err.response.data.message || "Terjadi kesalahan. Silakan coba lagi."
        this.loadCaptcha()
      }
    }
  }
}

</script>
