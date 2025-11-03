<template>
  <div id="app">
    <navbar v-if="mostrarNavbar"></navbar>
    <router-view></router-view>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </div>
</template>

<script>
import Navbar from './components/menu.vue'

export default {
  name: 'App',
  components: {
    Navbar
  },
  computed: {
    mostrarNavbar() {
      
      const rotasComNavbar = ['/home', '/listar',];
      return rotasComNavbar.includes(this.$route.path);
    }
  }
}
</script>

<style>
html,
body,
#app {
  height: 100%;
  margin: 0;
  padding: 0;
  background-color: #0b032e;
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
