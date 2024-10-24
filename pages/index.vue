<template>
    <div>
      <header>
        <nav>
          <div class="nav-container">
            <span class="logo" style="font-size:x-large;">
              SNAP-SELL <img src="~assets/images/pc.png" alt="Logo" class="logo-image" />
            </span>
            <button @click="toggleMenu" class="toggle-button">☰</button> <!-- Hamburger icon -->
            <ul class="nav-links" :class="{ 'active': isMenuActive }">
              <li><nuxt-link to="/login">Login</nuxt-link></li>
              <li><nuxt-link to="/signup">Signup</nuxt-link></li>
            </ul>
          </div>
        </nav>
      </header>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isMenuActive: false, // State to track the menu visibility
      };
    },
    methods: {
      toggleMenu() {
        this.isMenuActive = !this.isMenuActive; // Toggle menu visibility
      },
    },
  };
  </script>

<style scoped>
header {
  background-color: #ffffff; /* Change to your desired color */
  color: rgb(0, 0, 0);
  padding: 10px;
}

.nav-container {
  display: flex;
  justify-content: space-between; /* Aligns items to opposite ends */
  align-items: center; /* Vertically centers items */
}

.nav-links {
  list-style: none; /* Remove bullet points */
  display: flex; /* Align links horizontally */
  transition: max-height 0.3s ease; /* Smooth transition for dropdown */
}

.nav-links li {
  margin: 10px 0; /* Space between links */
}

.nav-links li a {
  color: rgb(0, 0, 0); /* Link color */
  text-decoration: none; /* Remove underline */
  padding: 10px 15px; /* Add padding for better spacing */
  border: 2px solid rgb(2, 2, 2); /* Create a border around the link */
  border-radius: 4px; /* Optional: add rounded corners */
  transition: background-color 0.3s; /* Smooth transition for hover effect */
}

.nav-links li a:hover {
  background-color: rgba(0, 0, 0, 0.1); /* Change background on hover */
}

.logo-image {
  width: 100px; /* Default logo width */
  height: auto; /* Keep the aspect ratio */
  margin-right: 8px; /* Space between the image and the text */
  vertical-align: middle; /* Align image with text */
}

.logo {
  text-decoration: overline;
  font-size: x-large; /* Default font size for logo text */
}

/* Toggle Button */
.toggle-button {
  display: none; /* Hidden by default */
  font-size: 24px; /* Size of the button icon */
  background: none; /* Remove default button styles */
  border: none; /* Remove border */
  cursor: pointer; /* Change cursor to pointer */
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
  .nav-links {
    display: none; /* Hide links by default on small screens */
    flex-direction: column; /* Stack links vertically */
    position: absolute; /* Position links absolutely */
    background: white; /* Background color for links */
    right: 0; /* Align links to the right */
    top: 50px; /* Adjust based on header height */
    border: 1px solid rgb(2, 2, 2); /* Optional border */
    width: 150px; /* Set a width for the dropdown */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
  }

  .nav-links.active {
    display: flex; /* Show links when active */
  }

  .toggle-button {
    display: block; /* Show button on small screens */
  }

  .nav-links li a {
    font-size: 0.9em; /* Reduce font size for links */
    padding: 5px 10px; /* Reduce padding */
  }

  .logo-image {
    width: 80px; /* Reduce logo size */
  }

  .logo {
    font-size: large; /* Reduce logo text size */
  }
}

@media (max-width: 480px) {
  .nav-links li a {
    font-size: 0.8em; /* Further reduce font size for very small screens */
    padding: 2px 8px; /* Further reduce padding */
  }

  .logo-image {
    width: 60px; /* Further reduce logo size */
  }

  .logo {
    font-size: medium; /* Further reduce logo text size */
  }
}

/* Ensuring links are visible on larger screens */
@media (min-width: 769px) {
  .nav-links {
    display: flex; /* Always display links on larger screens */
  }
}
</style>


