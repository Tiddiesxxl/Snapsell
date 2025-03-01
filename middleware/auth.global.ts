export default defineNuxtRouteMiddleware(async (to, from) => {
  // Skip middleware if going to login page
  if (to.path === '/login') return;

  // Check if we're on the client side
  if (process.client) {
      const token = localStorage.getItem('token');
      
      if (!token) {
          return navigateTo('/login');
      }

      try {
          const response = await fetch('http://localhost/snapsell/auth.php?action=verify-session', {
              headers: {
                  'Authorization': `Bearer ${token}`
              }
          });

          const data = await response.json();
          
          if (!data.valid) {
              localStorage.removeItem('token');
              return navigateTo('/login');
          }
      } catch (error) {
          console.error('Auth check failed:', error);
          localStorage.removeItem('token');
          return navigateTo('/login');
      }
  }
});