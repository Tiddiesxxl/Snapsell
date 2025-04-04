<template>
  <div class="settings-container">
    <div class="settings-nav">
      <button 
        v-for="section in sections" 
        :key="section.id"
        :class="['nav-btn', { active: activeSection === section.id }]"
        @click="activeSection = section.id"
      >
        <Fa :icon="section.icon" />
        {{ section.name }}
      </button>
    </div>

    <div class="settings-content">
      <!-- Profile Settings -->
      <div v-if="activeSection === 'profile'" class="settings-section">
        <h2>Profile Settings</h2>
        <div class="profile-header">
          <div class="avatar-section">
            <img 
              :src="profileData.avatar || defaultAvatar" 
              alt="Profile" 
              class="profile-avatar"
              @error="handleImageError" 
            />
            <div class="avatar-overlay" @click="triggerAvatarUpload">
              <Fa icon="camera" />
              <span>Change Photo</span>
            </div>
            <input 
              type="file" 
              ref="avatarInput" 
              @change="handleAvatarChange" 
              accept="image/*" 
              class="hidden" 
            />
          </div>
          <div class="profile-info">
            <h3>{{ profileData.name }}</h3>
            <p>{{ profileData.email }}</p>
            <span class="membership-badge">{{ profileData.user_type }}</span>
          </div>
        </div>

        <form @submit.prevent="saveProfileSettings" class="settings-form">
          <div class="form-group">
            <label>Display Name</label>
            <input v-model="profileData.name" type="text" required />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Email</label>
              <input v-model="profileData.email" type="email" required />
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input 
                v-model="profileData.phone" 
                type="tel" 
                @input="validatePhone"
                pattern="[0-9]*"
                maxlength="15"
                placeholder="Enter phone number"
              />
            </div>
          </div>
          <div class="form-group">
            <label>Bio</label>
            <textarea v-model="profileData.bio" rows="4"></textarea>
          </div>
          <button type="submit" class="save-btn" :disabled="isSaving">
            {{ isSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>

      <!-- Account Settings -->
      <div v-if="activeSection === 'account'" class="settings-section">
        <h2>Account Settings</h2>
        
        <div class="settings-group">
          <h3>Password</h3>
          <form @submit.prevent="changePassword" class="settings-form">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" v-model="passwordData.current" />
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" v-model="passwordData.new" />
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" v-model="passwordData.confirm" />
            </div>
            <button type="submit" class="save-btn">Update Password</button>
          </form>
        </div>

        <div class="settings-group">
          <h3>Two-Factor Authentication</h3>
          <div class="toggle-setting">
            <span>Enable 2FA</span>
            <label class="switch">
              <input type="checkbox" v-model="securitySettings.twoFactor" />
              <span class="slider"></span>
            </label>
          </div>
        </div>

        <div class="settings-group danger-zone">
          <h3>Danger Zone</h3>
          <button @click="deactivateAccount" class="danger-btn">
            Deactivate Account
          </button>
          <button @click="deleteAccount" class="danger-btn">
            Delete Account
          </button>
        </div>
      </div>

      <!-- Notification Settings -->
      <div v-if="activeSection === 'notifications'" class="settings-section">
        <h2>Notification Settings</h2>
        
        <div class="settings-group">
          <h3>Email Notifications</h3>
          <div class="notification-options">
            <div class="toggle-setting" v-for="(value, key) in notificationSettings.email" :key="key">
              <span>{{ formatSettingName(key) }}</span>
              <label class="switch">
                <input type="checkbox" v-model="notificationSettings.email[key]" />
                <span class="slider"></span>
              </label>
            </div>
          </div>
        </div>

        <div class="settings-group">
          <h3>Push Notifications</h3>
          <div class="notification-options">
            <div class="toggle-setting" v-for="(value, key) in notificationSettings.push" :key="key">
              <span>{{ formatSettingName(key) }}</span>
              <label class="switch">
                <input type="checkbox" v-model="notificationSettings.push[key]" />
                <span class="slider"></span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Settings -->
      <div v-if="activeSection === 'payment'" class="settings-section">
        <h2>Payment Settings</h2>
        
        <div class="settings-group">
          <h3>Payment Methods</h3>
          <div class="payment-methods">
            <div v-for="method in paymentMethods" :key="method.id" class="payment-method">
              <div class="payment-info">
                <Fa icon="credit-card" />
                <span>{{ method.name }}</span>
                <span class="card-number">****{{ method.last4 }}</span>
              </div>
              <div class="payment-actions">
                <button @click="editPaymentMethod(method)" class="icon-btn">
                  <Fa icon="edit" />
                </button>
                <button @click="removePaymentMethod(method.id)" class="icon-btn danger">
                  <Fa icon="trash" />
                </button>
              </div>
            </div>
            <button @click="addPaymentMethod" class="add-payment-btn">
              <Fa icon="plus" /> Add Payment Method
            </button>
          </div>
        </div>

        <div class="settings-group">
          <h3>Payout Information</h3>
          <form @submit.prevent="savePayoutInfo" class="settings-form">
            <div class="form-group">
              <label>Bank Account Number</label>
              <input type="text" v-model="payoutInfo.accountNumber" />
            </div>
            <div class="form-group">
              <label>Routing Number</label>
              <input type="text" v-model="payoutInfo.routingNumber" />
            </div>
            <button type="submit" class="save-btn">Save Payout Information</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
import { useR2Storage } from '~/composables/useR2Storage';

const toast = useToast();
const avatarInput = ref(null);
const activeSection = ref('profile');
const isSaving = ref(false);
const defaultAvatar = 'https://i.pinimg.com/736x/1e/99/60/1e9960fc0554c6ab55869f2e7734611c.jpg';

const sections = [
  { id: 'profile', name: 'Profile', icon: 'user' },
  { id: 'account', name: 'Account', icon: 'shield-alt' },
  { id: 'notifications', name: 'Notifications', icon: 'bell' },
  { id: 'payment', name: 'Payment', icon: 'credit-card' }
];

// Profile Data
const profileData = ref({
  name: '',
  email: '',
  phone: '',
  bio: '',
  avatar: '',
  user_type: 'User'
});

// Security Settings
const passwordData = ref({
  current: '',
  new: '',
  confirm: ''
});

const securitySettings = ref({
  twoFactor: false
});

// Notification Settings
const notificationSettings = ref({
  email: {
    newSale: true,
    newMessage: true,
    eventReminders: true,
    marketingUpdates: false
  },
  push: {
    newSale: true,
    newMessage: true,
    eventReminders: true,
    marketingUpdates: false
  }
});

// Payment Methods
const paymentMethods = ref([
  {
    id: 1,
    type: 'visa',
    name: 'Visa ending in 4242',
    last4: '4242'
  }
]);

const payoutInfo = ref({
  accountNumber: '',
  routingNumber: ''
});

// Methods
const { uploadToR2, isUploading } = useR2Storage();

const triggerAvatarUpload = () => {
  avatarInput.value.click();
};

const handleAvatarChange = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // Validate file
  if (!file.type.startsWith('image/')) {
    toast.error('Please upload an image file');
    return;
  }

  if (file.size > 5 * 1024 * 1024) { // 5MB limit
    toast.error('Image size should be less than 5MB');
    return;
  }

  try {
    // Upload to R2
    const fileUrl = await uploadToR2(file, 'profiles');
    
    // Update profile with new image URL
    const token = localStorage.getItem('token');
    const response = await fetch('http://localhost/snapsell/user.php?action=update_profile_image', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        profile_picture: fileUrl
      })
    });

    const data = await response.json();
    if (data.success) {
      profileData.value.avatar = fileUrl;
      toast.success('Profile image updated successfully');
      
      // Update localStorage user data
      const userData = JSON.parse(localStorage.getItem('userData') || '{}');
      userData.profile_picture = fileUrl;
      localStorage.setItem('userData', JSON.stringify(userData));
    } else {
      toast.error(data.error || 'Failed to update profile image');
    }
  } catch (error) {
    console.error('Error uploading image:', error);
    toast.error('Failed to upload image');
  }
};

const validatePhone = (event) => {
  // Remove any non-numeric characters
  const value = event.target.value;
  profileData.value.phone = value.replace(/\D/g, '');
  
  // Optional: Format the phone number as you type
  // This example formats as: XXX-XXX-XXXX
  if (profileData.value.phone.length >= 10) {
    const formatted = profileData.value.phone.replace(
      /(\d{3})(\d{3})(\d{4})/,
      '$1-$2-$3'
    );
    profileData.value.phone = formatted;
  }
};

const saveProfileSettings = async () => {
  // Add phone validation
  if (profileData.value.phone && !/^\d{10,15}$/.test(profileData.value.phone.replace(/\D/g, ''))) {
    toast.error('Please enter a valid phone number');
    return;
  }

  try {
    isSaving.value = true;
    const token = localStorage.getItem('token');
    
    // Remove formatting before sending to server
    const phoneToSend = profileData.value.phone.replace(/\D/g, '');
    
    const response = await fetch('http://localhost/snapsell/user.php?action=update_profile', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        name: profileData.value.name,
        email: profileData.value.email,
        phone: phoneToSend,
        bio: profileData.value.bio
      })
    });

    const data = await response.json();
    if (data.success) {
      toast.success('Profile updated successfully');
      
      // Update localStorage user data
      const userData = JSON.parse(localStorage.getItem('userData') || '{}');
      userData.name = profileData.value.name;
      userData.email = profileData.value.email;
      userData.phone = profileData.value.phone;
      localStorage.setItem('userData', JSON.stringify(userData));
    } else {
      toast.error(data.error || 'Failed to update profile');
    }
  } catch (error) {
    console.error('Error saving profile:', error);
    toast.error('Failed to save profile changes');
  } finally {
    isSaving.value = false;
  }
};

const handleImageError = (e) => {
  e.target.src = defaultAvatar;
};

const changePassword = async () => {
  if (passwordData.value.new !== passwordData.value.confirm) {
    toast.error('Passwords do not match');
    return;
  }
  try {
    // API call to change password
    toast.success('Password updated successfully');
    passwordData.value = { current: '', new: '', confirm: '' };
  } catch (error) {
    toast.error('Failed to update password');
  }
};

const formatSettingName = (key) => {
  return key
    .split(/(?=[A-Z])/)
    .join(' ')
    .replace(/^\w/, c => c.toUpperCase());
};

const addPaymentMethod = () => {
  // Implement payment method addition logic
  toast.info('Add payment method functionality will be implemented soon');
};

const editPaymentMethod = (method) => {
  // Implement payment method editing logic
  toast.info(`Editing payment method: ${method.name}`);
};

const removePaymentMethod = async (id) => {
  try {
    // API call to remove payment method
    paymentMethods.value = paymentMethods.value.filter(method => method.id !== id);
    toast.success('Payment method removed successfully');
  } catch (error) {
    toast.error('Failed to remove payment method');
  }
};

const savePayoutInfo = async () => {
  try {
    // API call to save payout information
    toast.success('Payout information saved successfully');
  } catch (error) {
    toast.error('Failed to save payout information');
  }
};

const deactivateAccount = async () => {
  if (confirm('Are you sure you want to deactivate your account?')) {
    try {
      // API call to deactivate account
      toast.success('Account deactivated successfully');
    } catch (error) {
      toast.error('Failed to deactivate account');
    }
  }
};

const deleteAccount = async () => {
  if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
    try {
      // API call to delete account
      toast.success('Account deleted successfully');
    } catch (error) {
      toast.error('Failed to delete account');
    }
  }
};

// Fetch profile data
const fetchProfileData = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await fetch('http://localhost/snapsell/user.php?action=get_profile', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const data = await response.json();
    if (data.success) {
      profileData.value = {
        name: data.user.name || '',
        email: data.user.email || '',
        phone: data.user.phone_number || '',
        bio: data.user.bio || '',
        avatar: data.user.profile_picture || defaultAvatar,
        user_type: formatUserType(data.user.user_type || 'regular')
      };
    }
  } catch (error) {
    console.error('Error fetching profile:', error);
    toast.error('Failed to load profile data');
  }
};

const formatUserType = (type) => {
  return type.charAt(0).toUpperCase() + type.slice(1);
};

// Initialize component
onMounted(() => {
  fetchProfileData();
});
</script>

<style scoped>
.settings-container {
  display: flex;
  gap: 30px;
  padding: 20px;
}

.settings-nav {
  width: 200px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  border: none;
  border-radius: 8px;
  background: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.nav-btn.active {
  background: #11101D;
  color: white;
}

.settings-content {
  flex: 1;
  max-width: 800px;
  overflow: hidden; /* Prevent horizontal scroll */
}

.settings-section {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  max-height: calc(100vh - 100px); /* Adjust height to prevent overflow */
  overflow-y: auto; /* Enable scrolling */
}

.profile-header {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.avatar-section {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto;
}

.profile-avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(0,0,0,0.5);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.3s;
  cursor: pointer;
}

.avatar-overlay:hover {
  opacity: 1;
}

.hidden {
  display: none;
}

.settings-form {
  max-width: 600px;
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}

input, textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.save-btn {
  background: #11101D;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.settings-group {
  margin-bottom: 30px;
}

.toggle-setting {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #11101D;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.danger-zone {
  border-top: 1px solid #ddd;
  padding-top: 20px;
}

.danger-btn {
  background: #f44336;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 10px;
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.payment-method {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.payment-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.card-number {
  color: #666;
}

.payment-actions {
  display: flex;
  gap: 10px;
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
}

.icon-btn.danger {
  color: #f44336;
}

.add-payment-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px;
  border: 2px dashed #ddd;
  border-radius: 4px;
  background: none;
  cursor: pointer;
  margin-top: 10px;
}

@media (max-width: 768px) {
  .settings-container {
    flex-direction: column;
  }

  .settings-nav {
    width: 100%;
    flex-direction: row;
    overflow-x: auto;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}

.save-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.membership-badge {
  display: inline-block;
  padding: 4px 8px;
  background: #11101D;
  color: white;
  border-radius: 4px;
  font-size: 0.8rem;
  margin-top: 8px;
}

/* Add smooth scrolling */
.settings-section::-webkit-scrollbar {
  width: 8px;
}

.settings-section::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 8px;
}

.settings-section::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 8px;
}

.settings-section::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Base styles update */
.settings-section h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.settings-section h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.settings-form {
  max-width: 600px;
}

label {
  font-size: 0.9rem;
  margin-bottom: 6px;
}

input, textarea {
  font-size: 0.9rem;
  padding: 8px;
}

/* Mobile specific adjustments */
@media (max-width: 768px) {
  .settings-container {
    padding: 12px;
    gap: 15px;
  }

  .settings-section {
    padding: 15px;
  }

  .settings-section h2 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
  }

  .settings-section h3 {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
  }

  .nav-btn {
    padding: 8px;
    font-size: 0.9rem;
  }

  .nav-btn svg {
    font-size: 0.9rem;
  }

  .profile-header {
    gap: 15px;
    margin-bottom: 20px;
  }

  .avatar-section {
    width: 100px;
    height: 100px;
  }

  .profile-avatar {
    width: 100px;
    height: 100px;
  }

  .profile-info h3 {
    font-size: 1.1rem;
  }

  .profile-info p {
    font-size: 0.9rem;
  }

  .membership-badge {
    font-size: 0.75rem;
    padding: 3px 6px;
  }

  .form-group {
    margin-bottom: 15px;
  }

  label {
    font-size: 0.85rem;
    margin-bottom: 4px;
  }

  input, textarea {
    font-size: 0.85rem;
    padding: 6px;
  }

  .save-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
  }

  .toggle-setting {
    padding: 8px 0;
    font-size: 0.9rem;
  }

  .switch {
    width: 40px;
    height: 20px;
  }

  .slider:before {
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
  }

  input:checked + .slider:before {
    transform: translateX(20px);
  }

  .danger-btn {
    padding: 8px 16px;
    font-size: 0.9rem;
  }

  .payment-method {
    padding: 8px;
    font-size: 0.9rem;
  }

  .card-number {
    font-size: 0.85rem;
  }

  .add-payment-btn {
    padding: 8px;
    font-size: 0.9rem;
  }
}

/* Even smaller devices */
@media (max-width: 420px) {
  .settings-container {
    padding: 10px;
    gap: 12px;
  }

  .settings-section {
    padding: 12px;
  }

  .settings-section h2 {
    font-size: 1.2rem;
  }

  .settings-section h3 {
    font-size: 1rem;
  }

  .nav-btn {
    padding: 6px;
    font-size: 0.85rem;
  }

  .nav-btn svg {
    font-size: 0.85rem;
  }

  .avatar-section {
    width: 80px;
    height: 80px;
  }

  .profile-avatar {
    width: 80px;
    height: 80px;
  }

  .profile-info h3 {
    font-size: 1rem;
  }

  .profile-info p {
    font-size: 0.85rem;
  }

  .membership-badge {
    font-size: 0.7rem;
    padding: 2px 5px;
  }

  label {
    font-size: 0.8rem;
  }

  input, textarea {
    font-size: 0.8rem;
    padding: 5px;
  }

  .save-btn {
    padding: 6px 12px;
    font-size: 0.85rem;
  }

  .toggle-setting {
    font-size: 0.85rem;
  }

  .danger-btn {
    padding: 6px 12px;
    font-size: 0.85rem;
  }

  .payment-method {
    padding: 6px;
    font-size: 0.85rem;
  }

  .card-number {
    font-size: 0.8rem;
  }

  .add-payment-btn {
    padding: 6px;
    font-size: 0.85rem;
  }
}
</style>
