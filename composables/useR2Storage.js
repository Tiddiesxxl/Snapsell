import { ref } from 'vue';

export const useR2Storage = () => {
  const isUploading = ref(false);
  const uploadProgress = ref(0);

  const uploadToR2 = async (file, path = '') => {
    try {
      isUploading.value = true;
      const formData = new FormData();
      formData.append('file', file);
      formData.append('path', path);

      const response = await fetch('http://localhost/snapsell/storage.php?action=upload', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: formData
      });

      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Upload failed');
      }

      return data.url; // Return the CDN URL of the uploaded file
    } catch (error) {
      throw new Error(`Upload failed: ${error.message}`);
    } finally {
      isUploading.value = false;
      uploadProgress.value = 0;
    }
  };

  const deleteFromR2 = async (fileUrl) => {
    try {
      const response = await fetch('http://localhost/snapsell/storage.php?action=delete', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fileUrl })
      });

      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Delete failed');
      }

      return true;
    } catch (error) {
      throw new Error(`Delete failed: ${error.message}`);
    }
  };

  return {
    isUploading,
    uploadProgress,
    uploadToR2,
    deleteFromR2
  };
}; 