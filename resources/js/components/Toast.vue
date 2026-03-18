<template>
  <TransitionGroup 
    tag="div" 
    name="toast" 
    class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-[320px]"
  >
    <div 
      v-for="toast in toasts" 
      :key="toast.id"
      class="flex items-center gap-3 p-4 rounded-xl shadow-lg border backdrop-blur-md transition-all duration-300"
      :class="{
        'bg-emerald-50/90 border-emerald-200 text-emerald-800': toast.type === 'success',
        'bg-rose-50/90 border-rose-200 text-rose-800': toast.type === 'error',
        'bg-blue-50/90 border-blue-200 text-blue-800': toast.type === 'info'
      }"
    >
      <div class="flex-shrink-0">
        <i v-if="toast.type === 'success'" class="fas fa-check-circle text-emerald-500"></i>
        <i v-else-if="toast.type === 'error'" class="fas fa-exclamation-circle text-rose-500"></i>
        <i v-else class="fas fa-info-circle text-blue-500"></i>
      </div>
      
      <div class="flex-1">
        <p class="text-xs font-bold leading-tight">{{ toast.title }}</p>
        <p class="text-[10px] opacity-80 mt-0.5">{{ toast.message }}</p>
      </div>

      <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600">
        <i class="fas fa-times text-[10px]"></i>
      </button>
    </div>
  </TransitionGroup>
</template>

<script>
export default {
  data() {
    return {
      toasts: [],
      nextId: 0
    }
  },
  methods: {
    add(type, title, message, duration = 4000) {
      const id = this.nextId++;
      this.toasts.push({ id, type, title, message });
      
      setTimeout(() => {
        this.remove(id);
      }, duration);
    },
    remove(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    }
  }
}
</script>

<style scoped>
.toast-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.toast-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>