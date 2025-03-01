<template>
  <div class="gallery-container">
    <div class="gallery-header">
      <h2>My Gallery</h2>
      <div class="gallery-actions">
        <div class="search-bar">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search photos..."
          />
          <Fa icon="search" />
        </div>
        <div class="filter-options">
          <select v-model="selectedCategory">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat" :value="cat">
              {{ cat }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="gallery-grid">
      <div v-for="photo in filteredPhotos" 
           :key="photo.id" 
           class="photo-card">
        <img :src="photo.url" :alt="photo.title" />
        <div class="photo-details">
          <h3>{{ photo.title }}</h3>
          <p>{{ photo.category }}</p>
          <div class="photo-actions">
            <button @click="editPhoto(photo)">
              <Fa icon="edit" />
            </button>
            <button @click="deletePhoto(photo.id)">
              <Fa icon="trash" />
            </button>
            <button @click="togglePhotoVisibility(photo)">
              <Fa :icon="photo.isPublic ? 'eye' : 'eye-slash'" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Photo Modal -->
    <div v-if="showEditModal" class="modal">
      <div class="modal-content">
        <h3>Edit Photo</h3>
        <input v-model="editingPhoto.title" placeholder="Photo title" />
        <select v-model="editingPhoto.category">
          <option v-for="cat in categories" :key="cat" :value="cat">
            {{ cat }}
          </option>
        </select>
        <textarea v-model="editingPhoto.description" placeholder="Description"></textarea>
        <div class="modal-actions">
          <button @click="savePhotoEdit">Save</button>
          <button @click="showEditModal = false">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const searchQuery = ref('');
const selectedCategory = ref('');
const showEditModal = ref(false);
const editingPhoto = ref(null);

const categories = ['Portraits', 'Landscapes', 'Events', 'Wildlife', 'Street'];

// Sample data - replace with your API call
const photos = ref([
  {
    id: 1,
    title: 'Sample Photo 1',
    url: 'sample-url-1',
    category: 'Portraits',
    isPublic: true
  }
  // Add more photos
]);

const filteredPhotos = computed(() => {
  return photos.value.filter(photo => {
    const matchesSearch = photo.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = !selectedCategory.value || photo.category === selectedCategory.value;
    return matchesSearch && matchesCategory;
  });
});

const editPhoto = (photo) => {
  editingPhoto.value = { ...photo };
  showEditModal.value = true;
};

const savePhotoEdit = async () => {
  try {
    // Add your API call here
    const response = await fetch(`your-api-endpoint/photos/${editingPhoto.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(editingPhoto.value)
    });

    const data = await response.json();
    if (data.success) {
      const index = photos.value.findIndex(p => p.id === editingPhoto.value.id);
      photos.value[index] = { ...editingPhoto.value };
      showEditModal.value = false;
      toast.success('Photo updated successfully!');
    }
  } catch (error) {
    toast.error('Failed to update photo');
  }
};

const deletePhoto = async (photoId) => {
  if (!confirm('Are you sure you want to delete this photo?')) return;

  try {
    // Add your API call here
    const response = await fetch(`your-api-endpoint/photos/${photoId}`, {
      method: 'DELETE'
    });

    const data = await response.json();
    if (data.success) {
      photos.value = photos.value.filter(p => p.id !== photoId);
      toast.success('Photo deleted successfully!');
    }
  } catch (error) {
    toast.error('Failed to delete photo');
  }
};

const togglePhotoVisibility = async (photo) => {
  try {
    // Add your API call here
    const response = await fetch(`your-api-endpoint/photos/${photo.id}/visibility`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ isPublic: !photo.isPublic })
    });

    const data = await response.json();
    if (data.success) {
      photo.isPublic = !photo.isPublic;
      toast.success(`Photo is now ${photo.isPublic ? 'public' : 'private'}`);
    }
  } catch (error) {
    toast.error('Failed to update photo visibility');
  }
};
</script>

<style scoped>
.gallery-container {
  padding: 20px;
}

.gallery-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.gallery-actions {
  display: flex;
  gap: 20px;
}

.search-bar {
  position: relative;
}

.search-bar input {
  padding: 8px 32px 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.search-bar i {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.photo-card {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.photo-card:hover {
  transform: translateY(-5px);
}

.photo-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.photo-details {
  padding: 15px;
}

.photo-actions {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.photo-actions button {
  padding: 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s ease;
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
  max-width: 500px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
</style>
