<template>
  <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center align-items-center bg-dark text-center">
    <div class="p-5 rounded shadow bg-dark" style="max-width: 500px;">
      <div class="mb-4">
        <i class="bi bi-shield-lock-fill text-danger" style="font-size: 3rem;"></i>
      </div>
      <h2 class="text-danger mb-3">Acesso Negado</h2>
      <p class="mb-4 text-light">
        Você não tem permissão para acessar esta página.<br />
        Você será redirecionado em <strong>{{ countdown }}</strong> segundos.
      </p>
      <button class="btn btn-primary" @click="goHome">Ir agora</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      countdown: 10,
      timer: null
    };
  },
  mounted() {
    this.startCountdown();
  },
  methods: {
    startCountdown() {
      this.timer = setInterval(() => {
        if (this.countdown > 1) {
          this.countdown--;
        } else {
          this.clearTimer();
          this.goHome();
        }
      }, 1000);
    },
    clearTimer() {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    },
    goHome() {
      this.clearTimer();
      this.$router.push('/');
    }
  },
  beforeUnmount() {
    this.clearTimer(); 
  }
}
</script>