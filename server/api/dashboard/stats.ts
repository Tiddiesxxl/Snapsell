export default defineEventHandler(async (event) => {
  // Mock data for dashboard stats
  return {
    stats: {
      totalPhotos: 156,
      totalSales: 2450,
      activeEvents: 8,
      newMessages: 12
    },
    activities: [
      {
        id: 1,
        type: 'sale',
        description: 'New photo sale',
        amount: 49.99,
        time: '2 hours ago'
      },
      {
        id: 2,
        type: 'event',
        description: 'New event booking',
        amount: 299.99,
        time: '4 hours ago'
      },
      {
        id: 3,
        type: 'message',
        description: 'New client message',
        time: '6 hours ago'
      }
    ],
    categoryChart: {
      labels: ['Portraits', 'Events', 'Landscapes', 'Other'],
      datasets: [{
        data: [30, 40, 20, 10],
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
      }]
    },
    topItems: [
      {
        id: 1,
        name: 'Wedding Collection',
        category: 'Events',
        thumbnail: '/images/wedding.jpg',
        sales: 125,
        revenue: 12500,
        growth: 15
      },
      {
        id: 2,
        name: 'Portrait Session',
        category: 'Portraits',
        thumbnail: '/images/portrait.jpg',
        sales: 98,
        revenue: 4900,
        growth: 8
      }
    ]
  }
}) 