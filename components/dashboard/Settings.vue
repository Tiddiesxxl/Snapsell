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
            <img :src="profileData.avatar" alt="Profile" class="profile-avatar" />
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
            <span class="membership-badge">{{ profileData.membershipType }}</span>
          </div>
        </div>

        <form @submit.prevent="saveProfileSettings" class="settings-form">
          <div class="form-group">
            <label>Display Name</label>
            <input v-model="profileData.name" type="text" />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Email</label>
              <input v-model="profileData.email" type="email" />
            </div>
            <div class="form-group">
              <label>Phone</label>
              <input v-model="profileData.phone" type="tel" />
            </div>
          </div>
          <div class="form-group">
            <label>Bio</label>
            <textarea v-model="profileData.bio" rows="4"></textarea>
          </div>
          <button type="submit" class="save-btn">Save Changes</button>
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
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const avatarInput = ref(null);
const activeSection = ref('profile');

const sections = [
  { id: 'profile', name: 'Profile', icon: 'user' },
  { id: 'account', name: 'Account', icon: 'shield-alt' },
  { id: 'notifications', name: 'Notifications', icon: 'bell' },
  { id: 'payment', name: 'Payment', icon: 'credit-card' }
];

// Profile Data
const profileData = ref({
  name: 'John Doe',
  email: 'john@example.com',
  phone: '+1234567890',
  bio: '',
  avatar: 'https://i.pravatar.cc/150',
  membershipType: 'Pro Member'
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
const triggerAvatarUpload = () => {
  avatarInput.value.click();
};

const handleAvatarChange = async (event) => {
  const file = event.target.files[0];
  if (file) {
    try {
      // Handle avatar upload
      toast.success('Profile photo updated successfully');
    } catch (error) {
      toast.error('Failed to update profile photo');
    }
  }
};

const saveProfileSettings = async () => {
  try {
    // API call to save profile settings
    toast.success('Profile settings saved successfully');
  } catch (error) {
    toast.error('Failed to save profile settings');
  }
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
}

.settings-section {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-header {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.avatar-section {
  position: relative;
  width: 120px;
  height: 120px;
}

.profile-avatar {
  width: 100%;
  height: 100%;
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
  justify-content: center;
  align-items: center;
  color: white;
  opacity: 0;
  cursor: pointer;
  transition: opacity 0.3s ease;
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
</style>
