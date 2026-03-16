<template>
  <div class="fixed inset-0 w-full flex flex-col md:flex-row bg-[#1e60ff] font-sans overflow-hidden">
    <div class="hidden md:flex md:w-1/2 relative overflow-hidden h-full">
      <div class="relative z-10 p-10 lg:p-14 flex flex-col justify-between text-white w-full h-full">
        <div class="flex items-center gap-4">
          <div class="bg-white/10 p-2 rounded-xl backdrop-blur-sm border border-white/20">
            <img :src="Background" class="w-8 h-8 brightness-0 invert" />
          </div>
          <span class="text-xl font-bold tracking-wider uppercase">Sistem Stream</span>
        </div>

        <div class="max-w-md">
          <h2 class="text-4xl lg:text-5xl font-extrabold mb-6 leading-tight">
            Kelola Stream <br />
            <span class="opacity-80">Lebih Terstruktur.</span>
          </h2>
          <p class="text-blue-100 text-lg leading-relaxed opacity-90">
            Platform manajemen cerdas untuk Monitoring, Manage, dan Care Player secara lebih baik dan efisien.
          </p>
        </div>

        <p class="text-sm text-blue-200 font-medium tracking-widest">© 2026 Vasta Technology</p>
      </div>
    </div>

    <div class="w-full md:w-1/2 h-full flex items-center justify-center bg-white p-6 md:p-10 lg:p-14">
      <div class="w-full max-w-md animate-slide-in">
        <div class="mb-8 text-center md:text-left">
          <h3 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">Sign In</h3>
          <p class="text-gray-500 text-sm md:text-base">Silakan masuk menggunakan akun terdaftar anda</p>
        </div>

        <form @submit.prevent="login" class="space-y-4">
          <div>
            <label class="block text-xs md:text-sm font-bold text-gray-700 mb-1.5 uppercase tracking-wide">
              Alamat Email <span class="text-red-500">*</span>
            </label>
            <div class="relative group">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                <i class="fas fa-envelope"></i>
              </span>
              <input v-model="form.email" type="email" required placeholder="email@perusahaan.com" 
                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all text-sm" />
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1.5">
              <label class="text-xs md:text-sm font-bold text-gray-700 uppercase tracking-wide">
                Password <span class="text-red-500">*</span>
              </label>
            </div>
            <div class="relative group">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-blue-600 transition-colors">
                <i class="fas fa-lock"></i>
              </span>
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••"
                class="w-full pl-12 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-600 outline-none transition-all text-sm" />
              <button type="button" @click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
              </button>
            </div>
          </div>

          <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
            <div class="flex items-center justify-between mb-2 bg-white p-2 rounded-lg border border-gray-100 shadow-sm">
              
              <div class="flex-grow flex justify-center">
                <img 
                  v-if="captchaUrl" 
                  :src="captchaUrl" 
                  alt="Captcha" 
                  class="h-10 rounded select-none pointer-events-none"
                />
              </div>

              <button @click="loadCaptcha" type="button" class="text-blue-600 p-2 hover:bg-blue-50 rounded-full transition-colors">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>

            <input v-model="captchaInput" placeholder="MASUKKAN KODE" maxlength="5" required
              class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-600 outline-none uppercase text-center tracking-[0.2em] font-bold text-sm" />
          </div>

          <button type="submit" :disabled="isLoading" 
            class="w-full bg-[#4f93af] hover:bg-[#3d7a92] text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-[0.99] disabled:opacity-50">
            {{ isLoading ? 'Memproses...' : 'MASUK KE SISTEM' }}
          </button>
        </form>

        <p v-if="message" :class="['mt-4 p-3 rounded-lg text-center text-sm border', messageClass]">
          {{ message }}
        </p>

        <p class="mt-8 text-center text-xs md:text-sm text-gray-500 font-medium">
          Belum memiliki akses?
          <router-link to="/register" class="text-blue-600 font-bold hover:underline">Register</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import Background from "@/assets/PLN.svg";
import axios from "axios";

export default {
  data() {
    return {
      form: { 
        email: "", 
        password: "" 
      },
      captchaUrl: "",
      captchaInput: "",
      showPassword: false,
      isLoading: false,
      message: "",
      messageClass: "",
      Background,
    }
  },
  mounted() {
    this.loadCaptcha();
  },
  methods: {
    loadCaptcha() {
      const baseUrl = "http://localhost:8000/api/captcha";
      this.captchaUrl = `${baseUrl}?t=${new Date().getTime()}`;
      this.captchaInput = "";
    },
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async login() {
      this.isLoading = true;
      this.message = "";
      
      try {
        const apiUrl = "http://localhost:8000/api/v1";
        const { data } = await axios.post(`${apiUrl}/login`, {
          ...this.form,
          captcha: this.captchaInput
        });

        localStorage.setItem("token", data.access_token);
        this.message = "Berhasil masuk! Mengalihkan...";
        this.messageClass = "bg-green-50 text-green-700 border-green-100";

        setTimeout(() => {
          this.$router.push("/dashboard");
        }, 1500);

      } catch (error) {
        this.message = error.response?.data?.message || "Login gagal, periksa Email/Password.";
        this.messageClass = "bg-red-50 text-red-700 border-red-100";
        
        this.loadCaptcha();
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>