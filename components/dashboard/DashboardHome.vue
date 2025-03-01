<template>
    <div class="dashboard-home">
      <div class="stats-grid">
        <div class="stat-card">
          <h3>Total Photos</h3>
          <p class="stat-number">{{ stats.totalPhotos }}</p>
        </div>
        <div class="stat-card">
          <h3>Total Sales</h3>
          <p class="stat-number">${{ stats.totalSales }}</p>
        </div>
        <div class="stat-card">
          <h3>Active Events</h3>
          <p class="stat-number">{{ stats.activeEvents }}</p>
        </div>
        <div class="stat-card">
          <h3>New Messages</h3>
          <p class="stat-number">{{ stats.newMessages }}</p>
        </div>
      </div>
      
      <div class="recent-activity">
        <h2>Recent Activity</h2>
        <div class="activity-list">
          <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
            <span class="activity-time">{{ activity.time }}</span>
            <p class="activity-text">{{ activity.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const stats = ref({
    totalPhotos: 0,
    totalSales: 0,
    activeEvents: 0,
    newMessages: 0
  });
  
  const recentActivities = ref([]);
  
  onMounted(async () => {
    try {
      const response = await fetch('/api/dashboard/stats');
      const data = await response.json();
      stats.value = data.stats;
      recentActivities.value = data.activities;
    } catch (error) {
      console.error('Failed to fetch dashboard data:', error);
    }
  });
  </script>