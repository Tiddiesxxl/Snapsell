<template>
  <DashboardLayout />
</template>

<script setup>
import DashboardLayout from '~/components/dashboard/DashboardLayout.vue';
</script>

<script>
import {defineComponent} from 'vue';
import sidebar from '../components/sidebar.vue';
 
export default defineComponent({
  components: {
    sidebar, // Register the Sidebar component
  },
  setup() {
    // Any setup logic can go here
    return {};
  },
});
</script>

<style>
/* Global styles for the dashboard page */
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  overflow: hidden;
}

#__nuxt {
  height: 100%;
}
</style>

<style scoped>
.dashboard-container {
  display: flex;
  min-height: 100vh;
}

.dashboard-content {
  flex: 1;
  padding: 20px;
  margin-left: 78px;
  transition: margin-left 0.5s ease;
}

.sidebar.open ~ .dashboard-content {
  margin-left: 250px;
}
</style>