<template>
  <div class="events-container">
    <div class="events-header">
      <h2>Event Management</h2>
      <button class="create-btn" @click="showCreateModal = true">
        <Fa icon="plus" /> Create Event
      </button>
    </div>

    <div class="events-grid">
      <div v-for="event in events" :key="event.id" class="event-card">
        <div class="event-image">
          <img :src="event.coverImage" :alt="event.title" />
          <div class="event-status" :class="event.status.toLowerCase()">
            {{ event.status }}
          </div>
        </div>
        <div class="event-details">
          <h3>{{ event.title }}</h3>
          <p class="event-date">
            <Fa icon="calendar" /> {{ formatDate(event.date) }}
          </p>
          <p class="event-location">
            <Fa icon="map-marker-alt" /> {{ event.location }}
          </p>
          <div class="event-stats">
            <div class="stat">
              <span>Tickets Sold</span>
              <strong>{{ event.ticketsSold }}/{{ event.totalTickets }}</strong>
            </div>
            <div class="stat">
              <span>Revenue</span>
              <strong>${{ event.revenue }}</strong>
            </div>
          </div>
          <div class="event-actions">
            <button @click="editEvent(event)">
              <Fa icon="edit" /> Edit
            </button>
            <button @click="manageTickets(event)">
              <Fa icon="ticket-alt" /> Tickets
            </button>
            <button @click="viewGallery(event)">
              <Fa icon="images" /> Gallery
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Event Modal -->
    <div v-if="showCreateModal" class="modal">
      <div class="modal-content">
        <h3>{{ editingEvent ? 'Edit Event' : 'Create New Event' }}</h3>
        <form @submit.prevent="saveEvent">
          <div class="form-group">
            <label>Event Title</label>
            <input v-model="eventForm.title" required />
          </div>
          <div class="form-group">
            <label>Date & Time</label>
            <input type="datetime-local" v-model="eventForm.date" required />
          </div>
          <div class="form-group">
            <label>Location</label>
            <input v-model="eventForm.location" required />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea v-model="eventForm.description" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>Ticket Types</label>
            <div v-for="(ticket, index) in eventForm.tickets" :key="index" class="ticket-type">
              <input v-model="ticket.name" placeholder="Ticket Name" />
              <input type="number" v-model="ticket.price" placeholder="Price" />
              <input type="number" v-model="ticket.quantity" placeholder="Quantity" />
              <button type="button" @click="removeTicketType(index)">
                <Fa icon="times" />
              </button>
            </div>
            <button type="button" @click="addTicketType" class="add-ticket-btn">
              <Fa icon="plus" /> Add Ticket Type
            </button>
          </div>
          <div class="modal-actions">
            <button type="submit" class="save-btn">Save Event</button>
            <button type="button" @click="showCreateModal = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const showCreateModal = ref(false);
const editingEvent = ref(null);
const events = ref([]);

const eventForm = ref({
  title: '',
  date: '',
  location: '',
  description: '',
  tickets: [
    { name: '', price: 0, quantity: 0 }
  ]
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const addTicketType = () => {
  eventForm.value.tickets.push({ name: '', price: 0, quantity: 0 });
};

const removeTicketType = (index) => {
  eventForm.value.tickets.splice(index, 1);
};

const saveEvent = async () => {
  try {
    const endpoint = editingEvent.value 
      ? `your-api-endpoint/events/${editingEvent.value.id}`
      : 'your-api-endpoint/events';
    
    const response = await fetch(endpoint, {
      method: editingEvent.value ? 'PUT' : 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(eventForm.value)
    });

    const data = await response.json();
    if (data.success) {
      toast.success(`Event ${editingEvent.value ? 'updated' : 'created'} successfully!`);
      showCreateModal.value = false;
      // Refresh events list
      fetchEvents();
    }
  } catch (error) {
    toast.error('Failed to save event');
  }
};

const fetchEvents = async () => {
  try {
    const response = await fetch('your-api-endpoint/events');
    const data = await response.json();
    events.value = data.events;
  } catch (error) {
    toast.error('Failed to fetch events');
  }
};

const editEvent = (event) => {
  editingEvent.value = event;
  eventForm.value = { ...event };
  showCreateModal.value = true;
};

const manageTickets = (event) => {
  // Implement ticket management logic
};

const viewGallery = (event) => {
  // Implement gallery view logic
};

// Fetch events on component mount
onMounted(() => {
  fetchEvents();
});
</script>

<style scoped>
.events-container {
  padding: 20px;
}

.events-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.create-btn {
  background: #11101D;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.event-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.event-image {
  position: relative;
  height: 200px;
}

.event-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.event-status {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.event-status.active {
  background: #4CAF50;
  color: white;
}

.event-status.draft {
  background: #FFC107;
  color: black;
}

.event-status.completed {
  background: #9E9E9E;
  color: white;
}

.event-details {
  padding: 15px;
}

.event-date, .event-location {
  color: #666;
  margin: 5px 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.event-stats {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.event-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

.event-actions button {
  flex: 1;
  padding: 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
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

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.ticket-type {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto;
  gap: 10px;
  margin-bottom: 10px;
}

.add-ticket-btn {
  width: 100%;
  padding: 8px;
  background: #f0f0f0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 10px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.save-btn {
  background: #11101D;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}
</style>
