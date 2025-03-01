<template>
  <div class="analytics-container">
    <h1>Analytics Dashboard</h1>
    
    <div class="filter-controls">
      <div class="date-range">
        <label for="dateRange">Date Range:</label>
        <select id="dateRange" v-model="dateRange">
          <option value="week">Last Week</option>
          <option value="month">Last Month</option>
          <option value="quarter">Last Quarter</option>
          <option value="year">Last Year</option>
        </select>
      </div>
    </div>
    
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Views</h3>
        <p class="stat-number">{{ stats.totalViews }}</p>
        <p class="stat-change" :class="stats.viewsChange >= 0 ? 'positive' : 'negative'">
          {{ stats.viewsChange >= 0 ? '+' : '' }}{{ stats.viewsChange }}%
        </p>
      </div>
      
      <div class="stat-card">
        <h3>Engagement Rate</h3>
        <p class="stat-number">{{ stats.engagementRate }}%</p>
        <p class="stat-change" :class="stats.engagementChange >= 0 ? 'positive' : 'negative'">
          {{ stats.engagementChange >= 0 ? '+' : '' }}{{ stats.engagementChange }}%
        </p>
      </div>
      
      <div class="stat-card">
        <h3>Conversion Rate</h3>
        <p class="stat-number">{{ stats.conversionRate }}%</p>
        <p class="stat-change" :class="stats.conversionChange >= 0 ? 'positive' : 'negative'">
          {{ stats.conversionChange >= 0 ? '+' : '' }}{{ stats.conversionChange }}%
        </p>
      </div>
      
      <div class="stat-card">
        <h3>Avg. Order Value</h3>
        <p class="stat-number">${{ stats.avgOrderValue }}</p>
        <p class="stat-change" :class="stats.avgOrderChange >= 0 ? 'positive' : 'negative'">
          {{ stats.avgOrderChange >= 0 ? '+' : '' }}{{ stats.avgOrderChange }}%
        </p>
      </div>
    </div>
    
    <div class="chart-container">
      <h2>Performance Trends</h2>
      <div class="chart-placeholder">
        <p>Chart visualization will appear here</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const dateRange = ref('month');

const stats = ref({
  totalViews: 12540,
  viewsChange: 12.5,
  engagementRate: 4.2,
  engagementChange: 0.8,
  conversionRate: 2.1,
  conversionChange: -0.3,
  avgOrderValue: 85.40,
  avgOrderChange: 5.2
});

const fetchAnalyticsData = async () => {
  try {
    // Simulate API call
    // In a real app, you would fetch from your backend
    console.log(`Fetching analytics data for range: ${dateRange.value}`);
    
    // Simulate loading delay
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // For demo purposes, we'll just use the default stats
    // In a real app, you would update stats.value with the response data
    
    toast.success('Analytics data updated');
  } catch (error) {
    console.error('Failed to fetch analytics data:', error);
    toast.error('Failed to load analytics data');
  }
};

watch(dateRange, () => {
  fetchAnalyticsData();
});

onMounted(() => {
  fetchAnalyticsData();
});
</script>

<style scoped>
.analytics-container {
  padding: 20px;
}

h1 {
  margin-bottom: 20px;
  color: #333;
}

.filter-controls {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 20px;
}

.date-range {
  display: flex;
  align-items: center;
  gap: 10px;
}

.date-range select {
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.stat-card h3 {
  margin-top: 0;
  color: #666;
  font-size: 16px;
}

.stat-number {
  font-size: 28px;
  font-weight: bold;
  margin: 10px 0;
  color: #333;
}

.stat-change {
  font-size: 14px;
  font-weight: 500;
}

.positive {
  color: #28a745;
}

.negative {
  color: #dc3545;
}

.chart-container {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.chart-placeholder {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border-radius: 4px;
  color: #666;
}
</style>
