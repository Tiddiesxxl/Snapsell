import { defineStore } from 'pinia';

export const useMediaStore = defineStore('media', {
  state: () => ({
    media: [],
    collections: [],
    loading: false,
    error: null,
    activeFilters: {
      mediaType: 'all', // 'photos', 'videos', 'all'
      category: null,
      eventId: null,
      timeframe: null,
    },
    selectedMedia: [],
    viewMode: localStorage.getItem('galleryViewMode') || 'grid', // 'grid' or 'list'
  }),

  getters: {
    // Get all photos
    photos: (state) => state.media.filter(item => item.type === 'photos'),
    
    // Get all videos
    videos: (state) => state.media.filter(item => item.type === 'videos'),
    
    // Get filtered media based on active filters
    filteredMedia: (state) => {
      let filtered = [...state.media];
      
      // Filter by media type
      if (state.activeFilters.mediaType !== 'all') {
        filtered = filtered.filter(item => item.type === state.activeFilters.mediaType);
      }
      
      // Filter by category
      if (state.activeFilters.category) {
        filtered = filtered.filter(item => item.category === state.activeFilters.category);
      }
      
      // Filter by event
      if (state.activeFilters.eventId) {
        filtered = filtered.filter(item => item.eventId === state.activeFilters.eventId);
      }
      
      // Filter by timeframe
      if (state.activeFilters.timeframe) {
        const now = new Date();
        let cutoffDate;
        
        switch (state.activeFilters.timeframe) {
          case 'today':
            cutoffDate = new Date(now.setHours(0, 0, 0, 0));
            break;
          case 'week':
            cutoffDate = new Date(now.setDate(now.getDate() - 7));
            break;
          case 'month':
            cutoffDate = new Date(now.setMonth(now.getMonth() - 1));
            break;
          case 'year':
            cutoffDate = new Date(now.setFullYear(now.getFullYear() - 1));
            break;
        }
        
        filtered = filtered.filter(item => {
          const uploadDate = new Date(item.uploadDate);
          return uploadDate >= cutoffDate;
        });
      }
      
      return filtered;
    },
    
    // Get media in a specific collection
    mediaInCollection: (state) => (collectionId) => {
      const collection = state.collections.find(c => c.id === collectionId);
      if (!collection) return [];
      
      return state.media.filter(item => collection.mediaIds.includes(item.url));
    },
    
    // Get media from a specific event
    mediaFromEvent: (state) => (eventId) => {
      return state.media.filter(item => item.eventId === eventId);
    },
    
    // Portfolio media
    portfolioMedia: (state) => {
      return state.media.filter(item => item.addToPortfolio);
    },
  },

  actions: {
    async fetchMedia() {
      this.loading = true;
      try {
        const response = await fetch('http://localhost/snapsell/media.php?action=get_all_media', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        const data = await response.json();
        if (data.success) {
          this.media = data.media;
        } else {
          throw new Error(data.message || 'Failed to fetch media');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error fetching media:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCollections() {
      this.loading = true;
      try {
        const response = await fetch('http://localhost/snapsell/media.php?action=get_collections', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        const data = await response.json();
        if (data.success) {
          this.collections = data.collections;
        } else {
          throw new Error(data.message || 'Failed to fetch collections');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error fetching collections:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async createCollection(collection) {
      try {
        const response = await fetch('http://localhost/snapsell/media.php?action=create_collection', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ collection })
        });
        
        const data = await response.json();
        if (data.success) {
          this.collections.push(collection);
          return true;
        } else {
          throw new Error(data.message || 'Failed to create collection');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error creating collection:', error);
        return false;
      }
    },
    
    async addMediaToCollection(mediaUrls, collectionId) {
      const collection = this.collections.find(c => c.id === collectionId);
      if (!collection) return false;
      
      try {
        const response = await fetch('http://localhost/snapsell/media.php?action=add_to_collection', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ 
            collectionId, 
            mediaUrls 
          })
        });
        
        const data = await response.json();
        if (data.success) {
          // Update local collection
          const index = this.collections.findIndex(c => c.id === collectionId);
          if (index >= 0) {
            this.collections[index].mediaIds = [
              ...new Set([...this.collections[index].mediaIds, ...mediaUrls])
            ];
          }
          return true;
        } else {
          throw new Error(data.message || 'Failed to add media to collection');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error adding media to collection:', error);
        return false;
      }
    },
    
    setFilter(filterType, value) {
      this.activeFilters[filterType] = value;
    },
    
    toggleMediaSelection(mediaUrl) {
      const index = this.selectedMedia.indexOf(mediaUrl);
      if (index === -1) {
        this.selectedMedia.push(mediaUrl);
      } else {
        this.selectedMedia.splice(index, 1);
      }
    },
    
    clearSelection() {
      this.selectedMedia = [];
    },
    
    setSelectedMedia(mediaUrls) {
      this.selectedMedia = [...mediaUrls];
    },
    
    setViewMode(mode) {
      this.viewMode = mode;
      localStorage.setItem('galleryViewMode', mode);
    },
    
    addMedia(newMedia) {
      // Ensure we don't duplicate media
      const newUrls = newMedia.map(item => item.url);
      const existingMedia = this.media.filter(item => !newUrls.includes(item.url));
      
      // Add upload date to new media
      const mediaWithDates = newMedia.map(item => ({
        ...item,
        uploadDate: new Date().toISOString()
      }));
      
      this.media = [...existingMedia, ...mediaWithDates];
    },
    
    async deleteMedia(mediaUrls) {
      try {
        const response = await fetch('http://localhost/snapsell/media.php?action=delete_media', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ mediaUrls })
        });
        
        const data = await response.json();
        if (data.success) {
          // Remove from media array
          this.media = this.media.filter(item => !mediaUrls.includes(item.url));
          
          // Remove from all collections
          this.collections = this.collections.map(collection => ({
            ...collection,
            mediaIds: collection.mediaIds.filter(id => !mediaUrls.includes(id))
          }));
          
          // Clear from selected media
          this.selectedMedia = this.selectedMedia.filter(url => !mediaUrls.includes(url));
          
          return true;
        } else {
          throw new Error(data.message || 'Failed to delete media');
        }
      } catch (error) {
        this.error = error.message;
        console.error('Error deleting media:', error);
        return false;
      }
    },
    
    // Smart collections
    async generateSmartCollection(params) {
      // Generate a smart collection based on parameters
      // This could be based on AI tags, event data, etc.
      const { name, description, criteria } = params;
      
      let filteredMedia = [...this.media];
      
      // Apply criteria filters
      if (criteria.mediaType) {
        filteredMedia = filteredMedia.filter(item => item.type === criteria.mediaType);
      }
      
      if (criteria.category) {
        filteredMedia = filteredMedia.filter(item => item.category === criteria.category);
      }
      
      if (criteria.tags && criteria.tags.length) {
        filteredMedia = filteredMedia.filter(item => {
          const mediaTags = item.metadata.tags.split(',').map(tag => tag.trim().toLowerCase());
          return criteria.tags.some(tag => mediaTags.includes(tag.toLowerCase()));
        });
      }
      
      // Create the smart collection
      const smartCollection = {
        id: `smart-${Date.now()}`,
        name,
        description,
        isSmartCollection: true,
        criteria,
        mediaIds: filteredMedia.map(item => item.url)
      };
      
      return this.createCollection(smartCollection);
    }
  },
}); 