<template>
  <div class="dashboard-layout">
    <sidebar @menu-change="handleMenuChange" />
    <main class="dashboard-content">
      <div v-if="currentComponentId === 'dashboard'">
        <DashboardHome />
      </div>
      <div v-else-if="currentComponentId === 'upload'">
        <PhotoUpload />
      </div>
      <div v-else-if="currentComponentId === 'gallery'">
        <Gallery />
      </div>
      <div v-else-if="currentComponentId === 'events'">
        <EventManager />
      </div>
      <div v-else-if="currentComponentId === 'sales'">
        <Sales />
      </div>
      <div v-else-if="currentComponentId === 'messages'">
        <Messages />
      </div>
      <div v-else-if="currentComponentId === 'analytics'">
        <Analytics />
      </div>
      <div v-else-if="currentComponentId === 'settings'">
        <Settings />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '~/components/sidebar.vue';
import DashboardHome from './DashboardHome.vue';
import PhotoUpload from './PhotoUpload.vue';
import Gallery from './Gallery.vue';
import EventManager from './EventManager.vue';
import Sales from './Sales.vue';
import Messages from './Messages.vue';
import Analytics from './Analytics.vue';
import Settings from './Settings.vue';

const currentComponentId = ref('dashboard');

const handleMenuChange = (menuId) => {
  console.log('Menu changed to:', menuId);
  currentComponentId.value = menuId;
};
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
}

.dashboard-content {
  flex: 1;
  padding: 20px;
  margin-left: 78px;
  transition: margin-left 0.3s ease;
  background: #f5f5f5;
}

.sidebar.open ~ .dashboard-content {
  margin-left: 250px;
}
</style> 