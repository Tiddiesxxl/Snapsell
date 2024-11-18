   // plugins/fontawesome.js
   import { library } from '@fortawesome/fontawesome-svg-core';
   import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
   import { fas } from '@fortawesome/free-solid-svg-icons'; // Import solid icons

   // Add all solid icons to the library
   library.add(fas);

   export default defineNuxtPlugin((nuxtApp) => {
     nuxtApp.vueApp.component('font-awesome-icon', FontAwesomeIcon); // Register the component
   });