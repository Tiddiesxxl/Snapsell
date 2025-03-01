<template>
  <div class="sidebar" :class="{ open: isOpen }">
    <div class="logo-details">
      <Fa :icon="['fas', 'camera']" class="icon" />
      <div class="logo_name">Snap-Sell</div>
      <Fa 
        :icon="isOpen ? 'times' : 'chevron-right'" 
        @click="toggleSidebar" 
        class="toggle-btn" 
      />
    </div>
    <ul class="nav-list">
      <li v-for="item in menuItems" :key="item.id" @click="navigateMenu(item.id)">
        <a href="#" :class="{ active: activeMenu === item.id }">
          <Fa :icon="item.icon" />
          <span class="links_name">{{ item.name }}</span>
        </a>
        <span class="tooltip">{{ item.name }}</span>
      </li>
      <li class="profile">
        <img :src="userImage" alt="profileImg" />
        <div class="profile-details">
          <div class="name_job">
            <div class="name">{{ userName }}</div>
          </div>
        </div>
        <Fa icon="sign-out-alt" id="log_out" class="logout-btn" @click="handleLogout" />
      </li>
    </ul>
  </div>
</template>
  
<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const isOpen = ref(false);
const userName = ref('User');
const userImage = ref('https://i.pinimg.com/736x/1e/99/60/1e9960fc0554c6ab55869f2e7734611c.jpg');
const activeMenu = ref('dashboard');

const emit = defineEmits(['menu-change']);

const toggleSidebar = () => {
  isOpen.value = !isOpen.value;
};

const navigateMenu = (menuId) => {
  if (activeMenu.value === menuId) return; // Prevent unnecessary updates
  
  activeMenu.value = menuId;
  emit('menu-change', menuId);
};

// Add logout functionality
const handleLogout = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      window.location.href = '/login';
      return;
    }

    const response = await fetch('http://localhost/snapsell/auth.php?action=logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    const data = await response.json();

    if (data.success) {
      localStorage.removeItem('token');
      localStorage.removeItem('userData');
      toast.success('Logged out successfully');
      window.location.href = '/login';
    } else {
      toast.error(data.error || 'Logout failed');
    }
  } catch (error) {
    console.error('Logout error:', error);
    toast.error('Network error occurred');
  }
};

// Get user data on component mount
onMounted(() => {
  // Get user data from localStorage
  const userDataStr = localStorage.getItem('userData');
  if (userDataStr) {
    try {
      const userData = JSON.parse(userDataStr);
      userName.value = userData.name || 'User';
    } catch (e) {
      console.error('Error parsing user data:', e);
    }
  }

  // Check session activity every minute
  const interval = setInterval(() => {
    const lastActive = parseInt(localStorage.getItem('lastActive') || '0');
    if (Date.now() - lastActive > 30 * 60 * 1000) { // 30 minutes inactivity
      handleLogout();
    }
  }, 60000);

  // Update activity timestamp on user interaction
  window.addEventListener('click', () => {
    localStorage.setItem('lastActive', Date.now().toString());
  });
});

const menuItems = [
  { id: 'dashboard', name: 'Dashboard', icon: 'tachometer-alt' },
  { id: 'upload', name: 'Upload Photos', icon: 'cloud-upload-alt' },
  { id: 'gallery', name: 'My Gallery', icon: 'images' },
  { id: 'events', name: 'Event Management', icon: 'calendar-alt' },
  { id: 'sales', name: 'Sales & Orders', icon: 'shopping-cart' },
  { id: 'messages', name: 'Messages', icon: 'comments' },
  { id: 'analytics', name: 'Analytics', icon: 'chart-line' },
  { id: 'settings', name: 'Settings', icon: 'cog' }
];
</script>
  
<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: 78px;
  background: #11101D;
  padding: 6px 14px;
  z-index: 99;
  transition: all 0.5s ease;
  display: flex;
  flex-direction: column;
}

.sidebar.open {
  width: 250px;
}

.sidebar .logo-details {
  height: 60px;
  min-height: 60px;
  display: flex;
  align-items: center;
  position: relative;
}

.sidebar .logo-details .icon {
  color: #fff;
  font-size: 28px;
  margin-right:10px;
}

.sidebar .logo-details .logo_name {
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  opacity: 0;
  transition: all 0.5s ease;
}

.sidebar.open .logo-details .logo_name {
  opacity: 1;
}

.sidebar .logo-details .toggle-btn {
  position: absolute;
  top: 50%;  /* Changed from 15px to 50% */
  transform: translateY(-50%);  /* Added to vertically center */
  right: 0;  /* Changed from 15px to 0 */
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.sidebar.open .logo-details .toggle-btn {
  transform: rotate(180deg);
}

.sidebar .nav-list {
  height: calc(100vh - 140px);
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  overflow: hidden;
  padding: 20px 0;
}

.sidebar li {
  position: relative;
  margin: 0;
}

.sidebar li .tooltip {
  position: absolute;
  top: -20px;
  left: calc(100% + 15px);
  z-index: 3;
  background: #fff;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 15px;
  font-weight: 400;
  opacity: 0;
  white-space: nowrap;
  pointer-events: none;
  transition: 0s;
}

.sidebar li:hover .tooltip {
  opacity: 1;
  pointer-events: auto;
  transition: all 0.4s ease;
  top: 50%;
  transform: translateY(-50%);
}

.sidebar.open li .tooltip {
  display: none;
}

.sidebar li a {
  display: flex;
  height: 100%;
  width: 100%;
  border-radius: 12px;
  align-items: center;
  text-decoration: none;
  transition: all 0.4s ease;
  background: #11101D;
}

.sidebar li a svg {
  margin-right: 15px;
  min-width: 20px;
}

.sidebar li a:hover {
  background: #FFF;
}

.sidebar li a .links_name {
  color: #fff;
  font-size: 15px;
  font-weight: 400;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: 0.4s;
}

.sidebar.open li a .links_name {
  opacity: 1;
  pointer-events: auto;
}

.sidebar li a:hover .links_name,
.sidebar li a:hover i {
  transition: all 0.5s ease;
  color: #11101D;
}

.sidebar li.profile {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 60px;
  min-height: 60px;
  padding: 10px 14px;
  background: #1d1b31;
  transition: all 0.5s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar li.profile img {
  height: 35px;
  width: 35px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #fff;
}

.sidebar li.profile .profile-details {
  display: none;
}

.sidebar li.profile #log_out {
  display: none;
}

/* Show profile details and adjust layout when sidebar is open */
.sidebar.open li.profile {
  justify-content: flex-start;
}

.sidebar.open li.profile .profile-details {
  display: flex;
  align-items: center;
  flex: 1;
  padding-left: 10px;
}

.sidebar.open li.profile img {
  margin-right: 12px;
}

.sidebar.open li.profile .name_job {
  color: #fff;
  font-size: 15px;
}

.sidebar.open li.profile .name {
  font-weight: 500;
  margin-bottom: 3px;
}

.sidebar.open .profile #log_out {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  top: 50%;
  right: 15px;
  transform: translateY(-50%);
  background: #1d1b31;
  width: 35px;
  height: 35px;
  line-height: 35px;
  border-radius: 50%;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.sidebar.open .profile #log_out:hover {
  background: #fff;
  color: #1d1b31;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
  .sidebar {
    width: 78px;
    padding: 6px 14px;
    height: 100vh;
    overflow: hidden;
  }

  .sidebar.open {
    width: 250px;
  }

  .sidebar .logo-details {
    height: 50px;
    min-height: 50px;
  }

  .sidebar .nav-list {
    height: calc(100vh - 110px);
    justify-content: flex-start;
    gap: 15px;
    padding: 10px 0;
  }

  .sidebar li {
    margin: 30px 0;
    height: auto;
  }

  .sidebar li a {
    padding: 5px 0;
    height: 35px;
    min-height: 35px;
  }

  .sidebar li.profile {
    height: 50px;
    min-height: 50px;
    padding: 8px;
  }

  .sidebar li.profile img {
    height: 30px;
    width: 30px;
  }

  .sidebar .logo_name {
    opacity: 1;
  }

  .sidebar li .tooltip {
    display: none;
  }

  .sidebar li a .links_name {
    opacity: 0;
  }

  .sidebar.open li a .links_name {
    opacity: 1;
  }

  .sidebar input {
    display: none;
  }

  .sidebar li {
    margin: 8px 0;
  }
}

@media (max-width: 420px) {
  .sidebar li .tooltip {
    display: none;
  }
}
</style>