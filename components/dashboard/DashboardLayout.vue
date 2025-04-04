<template>
  <div class="dashboard-layout" :class="{ 'sidebar-open': isSidebarOpen }">
    <sidebar @menu-change="handleMenuChange" v-model:isOpen="isSidebarOpen" />
    <main class="dashboard-content">
      <div class="content-wrapper">
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
const isSidebarOpen = ref(false);

const handleMenuChange = (menuId) => {
  console.log('Menu changed to:', menuId);
  currentComponentId.value = menuId;
};
</script>

<style scoped>
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  width: 100%;
  position: relative;
  overflow: hidden; /* Prevent body scroll when sidebar is open */
}

.dashboard-content {
  flex: 1;
  min-height: 100vh;
  margin-left: 78px;
  transition: margin-left 0.3s ease;
  background: #f5f5f5;
  position: relative;
  width: calc(100% - 78px);
}

.dashboard-layout.sidebar-open .dashboard-content {
  margin-left: 250px;
  width: calc(100% - 250px);
}

.content-wrapper {
  padding: 20px;
  height: 100%;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

/* Mobile Styles */
@media (max-width: 768px) {
  .dashboard-content {
    margin-left: 60px;
    width: calc(100% - 60px);
  }

  .dashboard-layout.sidebar-open .dashboard-content {
    margin-left: 200px;
    width: calc(100% - 200px);
  }

  .content-wrapper {
    padding: 15px;
  }
}

@media (max-width: 420px) {
  .dashboard-content {
    margin-left: 50px;
    width: calc(100% - 50px);
  }

  .dashboard-layout.sidebar-open .dashboard-content {
    margin-left: 180px;
    width: calc(100% - 180px);
  }

  .content-wrapper {
    padding: 10px;
  }
}

/* Fix for iOS height */
@supports (-webkit-touch-callout: none) {
  .dashboard-layout {
    min-height: -webkit-fill-available;
  }
}
</style> 