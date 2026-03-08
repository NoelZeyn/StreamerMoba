<template>
  <div>
    <router-view></router-view>
  </div>
</template>

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
