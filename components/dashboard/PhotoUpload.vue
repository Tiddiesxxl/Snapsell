<template>
  <div class="upload-container">
    <h2>Upload Photos</h2>
    
    <div class="upload-area" 
         @drop.prevent="handleDrop"
         @dragover.prevent="dragover = true"
         @dragleave.prevent="dragover = false"
         :class="{ 'drag-over': dragover }">
      <input type="file" 
             ref="fileInput" 
             @change="handleFileSelect" 
             multiple 
             accept="image/*" 
             class="file-input" />
      <div class="upload-content">
        <Fa icon="cloud-upload-alt" class="upload-icon" />
        <p>Drag and drop your photos here or click to browse</p>
      </div>
    </div>

    <div class="upload-preview" v-if="selectedFiles.length">
      <div v-for="(file, index) in selectedFiles" 
           :key="index" 
           class="preview-item">
        <img :src="file.preview" :alt="file.name" />
        <div class="preview-details">
          <span>{{ file.name }}</span>
          <button @click="removeFile(index)" class="remove-btn">
            <Fa icon="times" />
          </button>
        </div>
      </div>
    </div>

    <button @click="uploadFiles" 
            class="upload-btn" 
            :disabled="!selectedFiles.length">
      Upload {{ selectedFiles.length }} Photos
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import { useR2Storage } from '~/composables/useR2Storage';

const toast = useToast();
const fileInput = ref(null);
const selectedFiles = ref([]);
const dragover = ref(false);

const { uploadToR2, isUploading, uploadProgress } = useR2Storage();

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files);
  processFiles(files);
};

const handleDrop = (event) => {
  dragover.value = false;
  const files = Array.from(event.dataTransfer.files);
  processFiles(files);
};

const processFiles = (files) => {
  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        selectedFiles.value.push({
          file: file,
          preview: e.target.result,
          name: file.name
        });
      };
      reader.readAsDataURL(file);
    }
  });
};

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1);
};

const uploadFiles = async () => {
  try {
    const uploadedUrls = [];
    
    // Upload each file to R2
    for (const fileData of selectedFiles.value) {
      const fileUrl = await uploadToR2(fileData.file, 'gallery');
      uploadedUrls.push({
        url: fileUrl,
        name: fileData.name
      });
    }

    // Save uploaded files to your database
    const response = await fetch('http://localhost/snapsell/media.php?action=save_uploads', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        files: uploadedUrls
      })
    });

    const data = await response.json();
    if (data.success) {
      toast.success('Photos uploaded successfully!');
      selectedFiles.value = [];
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    toast.error('Failed to upload photos: ' + error.message);
  }
};
</script>

<style scoped>
.upload-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.upload-area {
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  margin: 20px 0;
  position: relative;
  transition: all 0.3s ease;
}

.drag-over {
  border-color: #11101D;
  background-color: rgba(17, 16, 29, 0.05);
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-content {
  pointer-events: none;
}

.upload-icon {
  font-size: 48px;
  color: #11101D;
  margin-bottom: 15px;
}

.upload-preview {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.preview-item {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-item img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.preview-details {
  padding: 10px;
  background: rgba(255,255,255,0.9);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.remove-btn {
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 5px;
}

.upload-btn {
  background: #11101D;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s ease;
}

.upload-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
