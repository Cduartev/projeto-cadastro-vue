<template>
  <div class="d-flex justify-content-end align-items-stretch min-vh-100 w-100 bg-container">
    <div class="form-container text-white p-4" style="width: 100vh;">
      <h3 class="text-center mb-4">Login</h3>

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" v-model="email" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Senha</label>
          <input type="password" class="form-control" id="password" v-model="password" required />
        </div>

        <div class="d-grid mb-3 ms-auto">
          <button type="submit" class="btn btn-primary">Entrar</button>
        </div>

        <div class="text-center">
          <a href="#" @click.prevent="forgotPassword">Esqueceu a senha?</a>
        </div>
      </form>

      <hr />

      <div class="text-center">
        <p>Não tem uma conta?</p>
        <button class="btn btn-outline-secondary" @click="goToRegister">Cadastrar</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {

    goToRegister() {
      this.$router.push({ name: 'cadastro' });
    },

    forgotPassword() {
      alert('Funcionalidade de recuperação de senha ainda não implementada.');
    },

    async handleLogin() {
      if (!this.email || !this.password) {
        alert('Preencha todos os campos');
        return;
      }

      try {
        const response = await axios.post('http://localhost/controller/index.php', {
          action: 'login_usuario',
          email: this.email,
          senha: this.password
        });

        // Debug: verificar a resposta do backend
        console.log('Resposta do backend:', response.data);

        if (response.data.success) {
          const { token, nome } = response.data;

          if (!token || !nome) {
            alert('Erro: token ou nome não retornados pelo servidor.');
            return;
          }

          // Salva token e nome no localStorage
          localStorage.setItem('token', token);
          localStorage.setItem('usuario_nome', nome);

          // Redireciona para /home
          this.$router.push({ name: 'home' });

        } else {
          alert(response.data.message || 'Erro ao logar.');
        }

      } catch (error) {
        console.error('Erro ao tentar logar:', error);
        alert('Erro ao tentar logar. Verifique se o servidor está rodando.');
      }
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
  position: relative;
}

.bg-container::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 0;
}

.form-container {
  position: relative;
  z-index: 1;
  background-color: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(20px);
}
</style>
