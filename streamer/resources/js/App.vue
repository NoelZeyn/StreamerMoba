<template>
  <div id="app">
    <router-view v-slot="{ Component }">
      <transition name="slide-fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>
<style>
/* Animasi Slide Smooth */
.slide-fade-enter-active {
  transition: all 0.5s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from {
  transform: translateX(50px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateX(-50px);
  opacity: 0;
}

body {
  overflow: hidden;
  margin: 0;
}
</style>

<script>
export default {
  created() {
    const token = localStorage.getItem('token');
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
  }
};
</script>
