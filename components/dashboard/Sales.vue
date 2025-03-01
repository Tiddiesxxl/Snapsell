<template>
    <div class="sales-container">
      <div class="sales-header">
        <h2>Sales & Orders</h2>
        <div class="header-actions">
          <div class="date-filter">
            <select v-model="dateRange">
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
              <option value="year">This Year</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>
          <button class="export-btn" @click="exportSales">
            <Fa icon="download" /> Export Report
          </button>
        </div>
      </div>
  
      <!-- Sales Overview Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon revenue">
            <Fa icon="dollar-sign" />
          </div>
          <div class="stat-info">
            <h3>Total Revenue</h3>
            <p class="stat-value">${{ stats.totalRevenue }}</p>
            <span class="stat-change" :class="stats.revenueChange >= 0 ? 'positive' : 'negative'">
              <Fa :icon="stats.revenueChange >= 0 ? 'arrow-up' : 'arrow-down'" />
              {{ Math.abs(stats.revenueChange) }}%
            </span>
          </div>
        </div>
  
        <div class="stat-card">
          <div class="stat-icon orders">
            <Fa icon="shopping-cart" />
          </div>
          <div class="stat-info">
            <h3>Total Orders</h3>
            <p class="stat-value">{{ stats.totalOrders }}</p>
            <span class="stat-change" :class="stats.ordersChange >= 0 ? 'positive' : 'negative'">
              <Fa :icon="stats.ordersChange >= 0 ? 'arrow-up' : 'arrow-down'" />
              {{ Math.abs(stats.ordersChange) }}%
            </span>
          </div>
        </div>
  
        <div class="stat-card">
          <div class="stat-icon photos">
            <Fa icon="images" />
          </div>
          <div class="stat-info">
            <h3>Photos Sold</h3>
            <p class="stat-value">{{ stats.photosSold }}</p>
            <span class="stat-change" :class="stats.photosChange >= 0 ? 'positive' : 'negative'">
              <Fa :icon="stats.photosChange >= 0 ? 'arrow-up' : 'arrow-down'" />
              {{ Math.abs(stats.photosChange) }}%
            </span>
          </div>
        </div>
      </div>
  
      <!-- Recent Orders Table -->
      <div class="orders-section">
        <h3>Recent Orders</h3>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" :key="order.id">
                <td>#{{ order.id }}</td>
                <td>{{ order.customer }}</td>
                <td>{{ formatDate(order.date) }}</td>
                <td>{{ order.items }}</td>
                <td>${{ order.total }}</td>
                <td>
                  <span class="status-badge" :class="order.status.toLowerCase()">
                    {{ order.status }}
                  </span>
                </td>
                <td>
                  <button @click="viewOrder(order)" class="action-btn">
                    <Fa icon="eye" />
                  </button>
                  <button @click="downloadInvoice(order)" class="action-btn">
                    <Fa icon="file-invoice" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  
      <!-- Order Details Modal -->
      <div v-if="selectedOrder" class="modal">
        <div class="modal-content">
          <h3>Order Details #{{ selectedOrder.id }}</h3>
          <div class="order-details">
            <!-- Order information -->
            <div class="detail-group">
              <label>Customer:</label>
              <p>{{ selectedOrder.customer }}</p>
            </div>
            <div class="detail-group">
              <label>Date:</label>
              <p>{{ formatDate(selectedOrder.date) }}</p>
            </div>
            <div class="detail-group">
              <label>Status:</label>
              <p>{{ selectedOrder.status }}</p>
            </div>
            
            <!-- Order items -->
            <div class="order-items">
              <h4>Items</h4>
              <div v-for="item in selectedOrder.itemDetails" :key="item.id" class="order-item">
                <img :src="item.thumbnail" :alt="item.name" />
                <div class="item-info">
                  <h5>{{ item.name }}</h5>
                  <p>{{ item.type }}</p>
                </div>
                <p class="item-price">${{ item.price }}</p>
              </div>
            </div>
  
            <div class="order-total">
              <h4>Total: ${{ selectedOrder.total }}</h4>
            </div>
          </div>
          <div class="modal-actions">
            <button @click="closeOrderDetails">Close</button>
            <button @click="downloadInvoice(selectedOrder)">Download Invoice</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useToast } from 'vue-toastification';
  
  const toast = useToast();
  const dateRange = ref('month');
  const selectedOrder = ref(null);
  
  const stats = ref({
    totalRevenue: 0,
    revenueChange: 0,
    totalOrders: 0,
    ordersChange: 0,
    photosSold: 0,
    photosChange: 0
  });
  
  const orders = ref([]);
  
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };
  
  const fetchSalesData = async () => {
    try {
      // Add your API endpoint
      const response = await fetch(`your-api-endpoint/sales?range=${dateRange.value}`);
      const data = await response.json();
      stats.value = data.stats;
      orders.value = data.orders;
    } catch (error) {
      toast.error('Failed to fetch sales data');
    }
  };
  
  const viewOrder = (order) => {
    selectedOrder.value = order;
  };
  
  const closeOrderDetails = () => {
    selectedOrder.value = null;
  };
  
  const downloadInvoice = async (order) => {
    try {
      // Add your invoice download logic
      const response = await fetch(`your-api-endpoint/invoices/${order.id}`);
      // Handle PDF download
      toast.success('Invoice downloaded successfully');
    } catch (error) {
      toast.error('Failed to download invoice');
    }
  };
  
  const exportSales = async () => {
    try {
      // Add your export logic
      const response = await fetch(`your-api-endpoint/sales/export?range=${dateRange.value}`);
      // Handle CSV/Excel download
      toast.success('Sales report exported successfully');
    } catch (error) {
      toast.error('Failed to export sales report');
    }
  };
  
  watch(dateRange, () => {
    fetchSalesData();
  });
  
  onMounted(() => {
    fetchSalesData();
  });
  </script>
  
  <style scoped>
  .sales-container {
    padding: 20px;
  }
  
  .sales-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .header-actions {
    display: flex;
    gap: 15px;
  }
  
  .date-filter select {
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ddd;
  }
  
  .export-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #11101D;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }
  
  .stat-icon.revenue {
    background: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
  }
  
  .stat-icon.orders {
    background: rgba(33, 150, 243, 0.1);
    color: #2196F3;
  }
  
  .stat-icon.photos {
    background: rgba(156, 39, 176, 0.1);
    color: #9C27B0;
  }
  
  .stat-value {
    font-size: 24px;
    font-weight: bold;
    margin: 5px 0;
  }
  
  .stat-change {
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  
  .stat-change.positive {
    color: #4CAF50;
  }
  
  .stat-change.negative {
    color: #f44336;
  }
  
  .table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-x: auto;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  th {
    background: #f5f5f5;
    font-weight: 600;
  }
  
  .status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
  }
  
  .status-badge.completed {
    background: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
  }
  
  .status-badge.pending {
    background: rgba(255, 193, 7, 0.1);
    color: #FFC107;
  }
  
  .status-badge.cancelled {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
  }
  
  .action-btn {
    padding: 6px;
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    transition: color 0.3s ease;
  }
  
  .action-btn:hover {
    color: #11101D;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
  }
  
  .order-details {
    margin: 20px 0;
  }
  
  .detail-group {
    margin-bottom: 15px;
  }
  
  .detail-group label {
    font-weight: 600;
    margin-right: 10px;
  }
  
  .order-items {
    margin: 20px 0;
  }
  
  .order-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
  }
  
  .order-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
  }
  
  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }
  
  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }
  
    .header-actions {
      flex-direction: column;
    }
  }
  </style>