import { defineStore } from 'pinia';

export const useEventStore = defineStore('event', {
  state: () => ({
    events: [],
    loading: false,
    error: null,
    activeEvent: null
  }),

  getters: {
    // Get all upcoming events
    upcomingEvents: (state) => {
      const now = new Date();
      return state.events.filter(event => new Date(event.startDate) > now)
        .sort((a, b) => new Date(a.startDate) - new Date(b.startDate));
    },
    
    // Get all past events
    pastEvents: (state) => {
      const now = new Date();
      return state.events.filter(event => new Date(event.endDate) < now)
        .sort((a, b) => new Date(b.endDate) - new Date(a.endDate)); // Most recent first
    },
    
    // Get current/ongoing events
    currentEvents: (state) => {
      const now = new Date();
      return state.events.filter(event => {
        const startDate = new Date(event.startDate);
        const endDate = new Date(event.endDate);
        return startDate <= now && endDate >= now;
      });
    },
    
    // Get event by ID
    getEventById: (state) => (id) => {
      return state.events.find(event => event.id === id) || null;
    },
    
    // Get events by type
    getEventsByType: (state) => (type) => {
      return state.events.filter(event => event.type === type);
    }
  },

  actions: {
    async fetchEvents() {
      this.loading = true;
      try {
        const response = await fetch('http://localhost/snapsell/events.php?action=get_events', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        const data = await response.json();
        if (data.success) {
          this.events = data.events;
        } else {
          throw new Error(data.message || 'Failed to fetch events');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error fetching events:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async createEvent(event) {
      try {
        const response = await fetch('http://localhost/snapsell/events.php?action=create_event', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ event })
        });
        
        const data = await response.json();
        if (data.success) {
          // Add the newly created event with the generated ID
          this.events.push({
            ...event,
            id: data.eventId
          });
          return data.eventId;
        } else {
          throw new Error(data.message || 'Failed to create event');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error creating event:', error);
        return null;
      }
    },
    
    async updateEvent(eventId, eventData) {
      try {
        const response = await fetch('http://localhost/snapsell/events.php?action=update_event', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ 
            eventId,
            eventData
          })
        });
        
        const data = await response.json();
        if (data.success) {
          // Update the event in the local state
          const index = this.events.findIndex(e => e.id === eventId);
          if (index !== -1) {
            this.events[index] = {
              ...this.events[index],
              ...eventData
            };
          }
          return true;
        } else {
          throw new Error(data.message || 'Failed to update event');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error updating event:', error);
        return false;
      }
    },
    
    async deleteEvent(eventId) {
      try {
        const response = await fetch('http://localhost/snapsell/events.php?action=delete_event', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ eventId })
        });
        
        const data = await response.json();
        if (data.success) {
          // Remove the event from the local state
          this.events = this.events.filter(e => e.id !== eventId);
          return true;
        } else {
          throw new Error(data.message || 'Failed to delete event');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error deleting event:', error);
        return false;
      }
    },
    
    async addAttendee(eventId, attendeeData) {
      try {
        const response = await fetch('http://localhost/snapsell/events.php?action=add_attendee', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ 
            eventId,
            attendee: attendeeData
          })
        });
        
        const data = await response.json();
        if (data.success) {
          // Update the event in the local state
          const index = this.events.findIndex(e => e.id === eventId);
          if (index !== -1) {
            if (!this.events[index].attendees) {
              this.events[index].attendees = [];
            }
            this.events[index].attendees.push({
              ...attendeeData,
              id: data.attendeeId
            });
          }
          return data.attendeeId;
        } else {
          throw new Error(data.message || 'Failed to add attendee');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error adding attendee:', error);
        return null;
      }
    },
    
    setActiveEvent(eventId) {
      this.activeEvent = eventId;
    },
    
    // For demo purposes - populate with sample events if none exist
    populateSampleEvents() {
      if (this.events.length === 0) {
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const nextWeek = new Date(now);
        nextWeek.setDate(nextWeek.getDate() + 7);
        
        const lastWeek = new Date(now);
        lastWeek.setDate(lastWeek.getDate() - 7);
        
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        
        this.events = [
          {
            id: 'event1',
            name: 'Summer Wedding Photoshoot',
            description: 'Wedding photography event for Johnson family',
            type: 'Wedding',
            location: 'Grand Plaza Hotel',
            startDate: tomorrow.toISOString(),
            endDate: tomorrow.toISOString(),
            status: 'upcoming',
            coverImage: 'https://images.unsplash.com/photo-1551871812-10ecc21ffa2f',
            attendees: []
          },
          {
            id: 'event2',
            name: 'Corporate Product Launch',
            description: 'New product photography for TechGiant Inc.',
            type: 'Corporate',
            location: 'TechGiant HQ',
            startDate: nextWeek.toISOString(),
            endDate: nextWeek.toISOString(),
            status: 'upcoming',
            coverImage: 'https://images.unsplash.com/photo-1551818255-e6e10975bc17',
            attendees: []
          },
          {
            id: 'event3',
            name: 'Nature Photography Workshop',
            description: 'Teaching landscape and wildlife photography techniques',
            type: 'Workshop',
            location: 'Mountain State Park',
            startDate: lastWeek.toISOString(),
            endDate: yesterday.toISOString(),
            status: 'completed',
            coverImage: 'https://images.unsplash.com/photo-1516298773066-c48f8e9bd92b',
            attendees: []
          },
          {
            id: 'event4',
            name: 'Portrait Session: Smith Family',
            description: 'Family portrait session for the Smith family',
            type: 'Portrait',
            location: 'Downtown Studio',
            startDate: now.toISOString(),
            endDate: now.toISOString(),
            status: 'ongoing',
            coverImage: 'https://images.unsplash.com/photo-1521122872341-065792fb2fa0',
            attendees: []
          }
        ];
      }
    }
  },
}); 