<template>
  <div class="container">
    <!-- Navbar -->
    <header>
      <nav class="navbar">
        <div class="logo">
          <img src="~assets/images/pc1.png" alt="Logo" class="logo-img" />
          <span>Snap Sell</span>
        </div>
        <div class="menu">
          <nuxt-link to="/login"  class="menu-link">LOGIN</nuxt-link>
          <nuxt-link to="/login?mode=signup" class="menu-link">SIGNUP</nuxt-link>
        </div>
        <!-- Mobile Menu Button -->
        <div class="menu-icon" @click="toggleMenu">☰</div>
      </nav>
      <!-- Responsive Menu -->
      <transition name="slide-fade">
        <div v-if="showMenu" class="mobile-menu">
          <div class="menu-header">
            <div class="close-icon" @click="toggleMenu">×</div>
          </div>
          <nuxt-link to="/login"  class="menu-link">LOGIN</nuxt-link>
          <nuxt-link to="/login?mode=signup" class="menu-link">SIGNUP</nuxt-link>
        </div>
      </transition>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-text">
        <h1>Capture Moments, Share Memories</h1>
        <p>Your go-to platform for professional and personal photography.</p>
        <button class="cta-button">Explore Collections</button>
      </div>
      <div class="hero-image">
        <img src="~assets/images/2nd.jpg" alt="Photography hero" />
      </div>
    </section>

    <!-- Main Content -->
    <main>
      <!-- Popular Categories Section -->
      <section class="categories">
        <h2>Popular Categories</h2>
        <div class="category-cards">
          <div class="category-card" v-for="category in categories" :key="category.name">
            <img :src="category.image" :alt="category.name" />
            <h3>{{ category.name }}</h3>
          </div>
        </div>
      </section>

      <!-- Featured Photographers Section -->
      <section class="trending">
        <h2>Featured Photographers</h2>
        <div class="photographer-cards">
          <div class="photographer-card" v-for="photographer in photographers" :key="photographer.name">
            <img :src="photographer.image" :alt="photographer.name" />
            <div class="info">
              <h3>{{ photographer.name }}</h3>
              <p>{{ photographer.specialty }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script>

export default {
  data() {
    return {
      categories: [
        { name: "Portraits", image: "https://i.pinimg.com/564x/1d/24/3e/1d243e02982bb3a941ce26b1bcf1143b.jpg" },
        { name: "Landscapes", image: "https://i.pinimg.com/564x/f3/b4/b8/f3b4b8e02a7b7ab2b1c9f320a51c070f.jpg" },
        { name: "Wildlife", image: "https://i.pinimg.com/564x/b7/f4/91/b7f4919408f9ebeb088018477a809f07.jpg" },
        { name: "Events", image: "https://i.pinimg.com/564x/03/7a/9d/037a9d8b6bcf8c4303b3b783a3d8ec17.jpg" },
      ],
      photographers: [
        { name: "Benedict Odhiambo", specialty: "Event Photographer", image: "https://i.postimg.cc/4dc87W4s/ben.jpg" },
      ],
      showMenu: false,
    };
  },
  methods: {
    toggleMenu() {
      this.showMenu = !this.showMenu;
    },
  },
};
</script>

<style scoped>
/* Base Styles */
.container {
  font-family: 'Poppins', sans-serif;
  color: #333;
  background-color: #f9f9f9;
  overflow-x: hidden;
}
.menu-header {
  position: absolute;
  top: 20px;
  right: 20px;
}
.close-icon {
  font-size: 24px;
  cursor: pointer;
  color: #555;
}
.menu-link {
  padding: 8px 16px; /* Add padding for spacing inside the border */
  border: 2px solid #555; /* Border color and thickness */
  border-radius: 5px; /* Rounded corners, optional */
  color: #555; /* Link text color */
  text-decoration: none;
  transition: background-color 0.3s, color 0.3s; /* Add a hover transition */
}

.menu-link:hover {
  background-color: #555; /* Background color on hover */
  color: #fff; /* Text color on hover */
}
header {
  background: #ffffff;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
  padding: 20px 0px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: fixed; /* Fixes the header to the top */
  top: 0;
  left: 0;
  width: 100%; /* Ensures the header spans the full width of the viewport */
  z-index: 10;
}
.navbar {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0 20px; /* Add padding for spacing */
}

.navbar .logo {
  display: flex;
  align-items: center;
  font-size: 24px;
  font-weight: bold;
  color: #555;
}

.logo-img {
  width: 50px;
  margin-right: 10px;
}
.logo {
  display: flex;
  align-items: center;
  font-size: 24px;
  font-weight: bold;
  color: #555;
  gap: 10px;
}

.menu {
  display: flex;
  gap: 40px;
  margin-left: auto; /* Push the menu to the right */
}

.menu-icon {
  display: none;
  font-size: 24px;
  cursor: pointer;
  margin-left: auto; /* This will push it to the right */
  padding-right: 20px;
}
.mobile-menu {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100vh;
  background-color: #ffffff;
  padding: 40px 20px;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.mobile-menu.active {
  transform: translateX(0);
}


.hero {
  display: flex;
  align-items: center;
  padding: 80px 40px;
  background: linear-gradient(135deg, #5f9ea0, #4b0082);
  flex-wrap: wrap;
}

.hero-text {
  flex: 1;
  color: #fff;
}

.hero-image img {
  width: 100%;
  max-width: 500px;
  border-radius: 15px;
}

/* Category Cards */
.categories .category-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  padding: 20px 0;
  align-items: center;
}

.category-card img, .photographer-card img {
  max-width: 100%; /* Ensure images do not exceed their parent width */
  height: auto; /* Maintain aspect ratio */
}

.category-card {
  width: 200px; /* Fixed width */
  height: 250px; /* Fixed height for uniformity */
  text-align: center;
  padding: 15px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden; /* Ensures that overflowing content is hidden */
}
.category-card img {
  width: 100%; /* Makes the image responsive */
  height: 170px; /* Fixed height for the image */
  object-fit: cover; /* Ensures the image covers the area without distortion */
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Photographer Cards */
.trending .photographer-cards {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  padding: 20px 0;
  align-items: center;
}

.photographer-card {
  width: 200px; /* Fixed width */
  height: 250px; /* Fixed height for uniformity */
  text-align: center;
  padding: 15px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden; /* Ensures that overflowing content is hidden */
}

.photographer-card img{
  width: 100%; /* Makes the image responsive */
  height: 170px; /* Fixed height for the image */
  object-fit: cover; /* Ensures the image covers the area without distortion */
}


.photographer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.info {
  text-align: center;
  margin-top: 10px;
}

/* Recent Searches */
.recent-searches .search-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.search-tag {
  background-color: #e0f7fa;
  padding: 8px 12px;
  border-radius: 20px;
  font-size: 0.9rem;
  color: #00796b;
  display: flex;
  align-items: center;
}

.close-btn {
  margin-left: 8px;
  cursor: pointer;
  color: #ff5252;
}

/* Updated transition classes */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: transform 0.3s ease;
}

.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(100%);
}

.slide-fade-enter-to,
.slide-fade-leave {
  transform: translateX(0);
}

/* Responsive Design */
@media (max-width: 768px) {
  .menu {
    display: none;
  }

  .menu-icon {
    display: block;
    margin-left: auto;
  }

  .hero {
    flex-direction: column;
    text-align: center;
  }

  .hero-text {
    margin-bottom: 20px;
  }
  .mobile-menu .menu-link {
    display: block;
    text-align: center;
    margin: 10px 0;
  }
  .category-cards,
  .photographer-cards {
    flex-direction: column;
  }
  .mobile-menu[v-if="showMenu"] {
    transform: translateX(0);
  }
}

@media (max-width: 480px) {
  header {
    padding: 10px 20px;
  }

  .hero-text h1 {
    font-size: 1.8rem;
  }

  .category-card img,
  .photographer-card img {
    height: 150px;
  }

  .search-tag {
    font-size: 0.8rem;
    padding: 8px 10px;
  }
}
</style>
