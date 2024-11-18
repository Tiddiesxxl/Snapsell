<template>
    <div class="sidebar" :class="{ open: isOpen }">
      <div class="logo-details">
        <font-awesome-icon icon="camera" class="icon" />
        <div class="logo_name">Snap-Sell</div>
        <font-awesome-icon 
  :icon="isOpen ? 'times' : 'chevron-right'" 
  @click="toggleSidebar" 
  class="toggle-btn" 
/>
      </div>
      <ul class="nav-list">
        <li>
          <a href="#">
            <font-awesome-icon icon="tachometer-alt" />
            <span class="links_name">Dashboard</span>
          </a>
          <span class="tooltip">Dashboard</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="user" />
            <span class="links_name">User</span>
          </a>
          <span class="tooltip">User</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="comments" />
            <span class="links_name">Messages</span>
          </a>
          <span class="tooltip">Messages</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="chart-line" />
            <span class="links_name">Analytics</span>
          </a>
          <span class="tooltip">Analytics</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="folder" />
            <span class="links_name">File Manager</span>
          </a>
          <span class="tooltip">Files</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="shopping-cart" />
            <span class="links_name">Order</span>
          </a>
          <span class="tooltip">Order</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="heart" />
            <span class="links_name">Saved</span>
          </a>
          <span class="tooltip">Saved</span>
        </li>
        <li>
          <a href="#">
            <font-awesome-icon icon="cog" />
            <span class="links_name">Setting</span>
          </a>
          <span class="tooltip">Setting</span>
        </li>
        <li class="profile">
          <div class="profile-details">
            <img src="https://i.pinimg.com/736x/1e/99/60/1e9960fc0554c6ab55869f2e7734611c.jpg" alt="profileImg" />
            <div class="name_job">
              <div class="name">Test</div>
            </div>
          </div>
          <font-awesome-icon icon="sign-out-alt" id="log_out" class="logout-btn" />
        </li>
      </ul>
    </div>
    <section class="home-section">
      <div class="text">Dashboard</div>
    </section>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isOpen: false,
      };
    },
    methods: {
      toggleSidebar() {
        this.isOpen = !this.isOpen;
      },
    },
  };
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
  height: 100%;
  width: 78px;
  background: #11101D;
  padding: 6px 14px;
  z-index: 99;
  transition: all 0.5s ease;
}

.sidebar.open {
  width: 250px;
}
.sidebar .logo-details .toggle-btn {
  position: absolute;
  top: 15px;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.3s ease;
  margin-right:10px;
}

.sidebar .logo-details {
  height: 60px;
  display: flex;
  align-items: center;
  position: relative;
  padding-right:40px;
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
  position: absolute; /* Ensures the toggle button stays inside the sidebar */
  top: 15px; /* Positions the button at the top inside the sidebar */
  right: 15px; /* Keeps it aligned to the right edge of the sidebar */
  color: #fff; /* Matches the sidebar theme */
  font-size: 20px; /* Adjusts icon size */
  cursor: pointer; /* Changes cursor to a pointer on hover */
  transition: transform 0.3s ease; /* Adds a smooth rotation animation */
}

.sidebar.open .logo-details .toggle-btn {
  transform: rotate(180deg); /* Optional rotation for effect */
}
.sidebar:not(.open) .logo-details .icon {
  display: none;
}

.sidebar .nav-list {
  margin-top: 20px;
  height: 100%;
}

.sidebar li {
  position: relative;
  margin: 8px 0;
  list-style: none;
}

.sidebar li {
  margin: 25px 0; /* Add gap between icons */
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
  position: fixed;
  height: 50px; /* Smaller profile */
  width: 68px; /* Adjusted size */
  left: 0;
  bottom: -8px;
  padding: 8px 12px; /* Adjusted padding */
  background: #1d1b31;
  transition: all 0.5s ease;
  overflow: hidden;
}

.sidebar.open li.profile {
  width: 200px; /* Adjusted for open state */
}

.sidebar li .profile-details {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
}

.sidebar li img {
  height: 35px; /* Smaller profile image */
  width: 35px; /* Smaller profile image */
  object-fit: cover;
  border-radius: 6px;
  margin-right: 10px;
}

.sidebar li.profile .name,
.sidebar li.profile .job {
  font-size: 15px;
  font-weight: 400;
  color: #fff;
  white-space: nowrap;
}

.sidebar li.profile .job {
  font-size: 12px;
}

.sidebar .profile #log_out {
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  background: #1d1b31;
  width: 30px; /* Smaller logout button */
  height: 30px; /* Smaller logout button */
  line-height: 30px; /* Center icon vertically */
  border-radius: 50%; /* Make it circular */
  transition: all 0.5s ease;
}

.sidebar.open .profile #log_out {
  width: 50px;
  background: none;
}

.home-section {
  position: relative;
  background: #E4E9F7;
  min-height: 100vh;
  top: 0;
  left: 60px;
  width: calc(100% - 78px);
  transition: all 0.5s ease;
  z-index: 2;
}

.sidebar.open ~ .home-section {
  left: 200px;
  width: calc(100% - 100px);
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
    width: 60px; /* Default width for mobile */
  }

  .sidebar.open {
    width: 200px; /* Expanded width for mobile */
  }

  .sidebar .logo_name {
    opacity: 1; /* Show logo name when open */
  }

  .sidebar li .tooltip {
    display: none; /* Hide tooltips on mobile */
  }

  .sidebar li a .links_name {
    opacity: 0; /* Hide link names when retracted */
  }

  .sidebar.open li a .links_name {
    opacity: 1; /* Show link names when open */
  }

  .sidebar input {
    display: none; /* Hide search bar on mobile */
  }
}

@media (max-width: 420px) {
  .sidebar li .tooltip {
    display: none; /* Hide tooltips on smaller screens */
  }
}

  </style>