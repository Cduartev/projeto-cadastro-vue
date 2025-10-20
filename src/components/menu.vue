<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light navbar-container">
      <i class="bi bi-cpu fs-2 ms-3 text-light"></i>
      <a class="navbar-brand ms-3" href="/home">Programas</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item active">
            <router-link class="nav-link" to="/home">Cadastrar</router-link>
          </li>
          <li class="nav-item active">
            <router-link class="nav-link" to="/listar">Lista de Pessoas</router-link>
          </li>
        </ul>

        <div class="d-flex align-items-center text-light me-3">
          <span class="me-3">Bem-vindo, {{ usuarioNome }}</span>
          <button class="btn btn-outline-light btn-sm" @click="logout">Sair</button>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  name: 'Navbar',
  data() {
    return {
      usuarioNome: ''
    };
  },
  mounted() {
    
    this.usuarioNome = localStorage.getItem('usuario_nome') || 'Usuário';
  },
  methods: {
    logout() {
      
      localStorage.removeItem('usuario_nome');
      localStorage.removeItem('usuario_id');

      
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
.navbar-brand {
  font-weight: bold;
  color: white;
}

.nav-link {
  color: white !important;
}

.navbar-toggler {
  border-color: white;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

.navbar-container{
background-color: rgba(0, 0, 0, 0.2); 
  backdrop-filter: blur(25px);
}
</style>