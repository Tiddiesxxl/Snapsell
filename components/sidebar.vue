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
      <li>
        <a href="#">
          <Fa icon="tachometer-alt" />
          <span class="links_name">Dashboard</span>
        </a>
        <span class="tooltip">Dashboard</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="comments" />
          <span class="links_name">Messages</span>
        </a>
        <span class="tooltip">Messages</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="chart-line" />
          <span class="links_name">Analytics</span>
        </a>
        <span class="tooltip">Analytics</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="folder" />
          <span class="links_name">File Manager</span>
        </a>
        <span class="tooltip">Files</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="shopping-cart" />
          <span class="links_name">Sales</span>
        </a>
        <span class="tooltip">Sales</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="heart" />
          <span class="links_name">Favourites</span>
        </a>
        <span class="tooltip">Favourites</span>
      </li>
      <li>
        <a href="#">
          <Fa icon="cog" />
          <span class="links_name">Setting</span>
        </a>
        <span class="tooltip">Setting</span>
      </li>
      <li class="profile">
        <img src="https://i.pinimg.com/736x/1e/99/60/1e9960fc0554c6ab55869f2e7734611c.jpg" alt="profileImg" />
        <div class="profile-details">
          <div class="name_job">
            <div class="name">Test</div>
          </div>
        </div>
        <Fa icon="sign-out-alt" id="log_out" class="logout-btn" @click="handleLogout" />
      </li>
    </ul>
  </div>
  <section class="home-section">
    <div class="text">Dashboard</div>
  </section>
</template>
  
<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const isOpen = ref(false);
const userName = ref('');
const userImage = ref('');

const toggleSidebar = () => {
  isOpen.value = !isOpen.value;
};

// Add logout functionality
const handleLogout = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      navigateTo('/login');
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
onMounted(async () => {
  const token = localStorage.getItem('token');
  if (token) {
    try {
      const response = await fetch('http://localhost/snapsell/auth.php?action=verify-session', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      const data = await response.json();
      if (data.valid && data.user) {
        userName.value = data.user.name;
        // If you have user image in the response
        // userImage.value = data.user.image;
      }
    } catch (error) {
      console.error('Failed to fetch user data:', error);
    }
  }
});
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

.home-section {
  position: fixed;
  background: #E4E9F7;
  min-height: 100vh;
  height: 100vh;
  top: 0;
  left: 78px;
  width: calc(100% - 78px);
  transition: all 0.5s ease;
  z-index: 2;
  overflow-y: auto;
}

.sidebar.open ~ .home-section {
  left: 250px;
  width: calc(100% - 250px);
}

.home-section .text {
  display: inline-block;
  color: #11101d;
  font-size: 25px;
  font-weight: 500;
  margin: 18px;
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

  .home-section {
    left: 78px;
    width: calc(100% - 78px);
    overflow-y: auto;
  }

  .sidebar.open ~ .home-section {
    left: 250px;
    width: calc(100% - 250px);
  }
}

@media (max-width: 420px) {
  .sidebar li .tooltip {
    display: none;
  }
}

  </style>