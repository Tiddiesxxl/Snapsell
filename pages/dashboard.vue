<template>
    <div>
        <sidebar/>
    </div>
</template>

<script setup>
definePageMeta({
  middleware: ['auth']
});
</script>

<script>
import {defineComponent} from 'vue';
import sidebar from '../components/sidebar.vue';
 
export default defineComponent({
  components: {
    sidebar, // Register the Sidebar component
  },
  setup() {
    // Any setup logic can go here
    return {};
  },
});
</script>

<style  scoped>

</style>