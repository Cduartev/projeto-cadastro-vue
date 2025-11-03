<template>
  <div class="d-flex justify-content-end align-items-stretch min-vh-100 w-100 bg-container">
    <div class="form-container text-white p-4" style="width: 100vh;">
      <h3 class="text-center mb-4">Cadastro</h3>

      <form @submit.prevent="handleRegister">
        <div class="mb-3">
          <label class="form-label">Usuário</label>
          <input
            type="text"
            class="form-control"
            v-model="user"
            required
          />
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            v-model="email"
            required
          />
        </div>

        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input
            type="password"
            class="form-control"
            v-model="password"
            required
          />
        </div>

        <div class="d-grid mb-3 ms-auto">
          <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>

        <div class="text-center">
          <a href="#" @click.prevent="goToLogin">Já tem uma conta? Login</a>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      user: '',
      email: '',
      password: ''
    };
  },
  methods: {
    async handleRegister() {
      try {
        const res = await axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
          action: 'cadastrar_usuario',
          nome: this.user,
          email: this.email,
          senha: this.password
        });

        if (res.data.success) {
          this.$toast.success(res.data.message);
          this.user = '';
          this.email = '';
          this.password = '';
        } else {
          this.$toast.success(res.data.message);
        }
      } catch (err) {
        this.$toast.error(err);
        this.$toast.error('Erro ao cadastrar usuário');
      }
    },
    goToLogin() {
      this.$router.push('/');
    }
  }
};
</script>

<style scoped>

.bg-container {
  background-image: url(../assets/windows-11-stock-official-colorful-3840x2160-5658.jpg); 
  background-size: cover; 
  background-position: center; 
  background-repeat: no-repeat;
}
.bg-container {
  position: relative;
}

.bg-container::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 0;
}

.bg-dark {
  position: relative;
  z-index: 1;
}

.form-container {
  background-color: rgba(0, 0, 0, 0.2); 
  backdrop-filter: blur(20px);
}


</style>