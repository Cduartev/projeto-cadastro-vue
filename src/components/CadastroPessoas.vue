<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white text-center">
            <h4>Cadastro De Clientes</h4>
          </div>

          <div class="card-body text-light bg-dark">
            <div class="form-group">
              <label for="nome">Nome:</label>
              <input type="text" id="nome" v-model="form.nome" class="form-control" placeholder="Digite Seu Nome" />
            </div>

            <div class="form-group">
              <label for="idade">Idade:</label>
              <input type="number" id="idade" v-model="form.idade" class="form-control"
                placeholder="Digite Sua Idade" />
            </div>

            <div class="form-group">
              <label for="trabalho">Profissão:</label>
              <input type="text" id="trabalho" v-model="form.profissao" class="form-control"
                placeholder="Digite Sua Profissão" />
            </div>
            <button class="btn btn-success btn-block mt-3" @click="cadastrar">
              Cadastrar <i class="bi bi-send"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "CadastroPessoas",
  data() {
    return {
      form: {
        nome: "",
        idade: "",
        profissao: "",
      },
      usuarios: [],
    };
  },
  mounted() {
    this.getPessoas();
  },
  methods: {
    getPessoas() {
      axios.post('http://localhost/controller/index.php', {
        action: 'listar'
      })
        .then(response => {
          this.usuarios = response.data;
        })
        .catch(error => {
          console.error('Erro ao buscar usuários:', error);
        });
    },

    cadastrar() {
      const { nome, idade, profissao } = this.form;

      if (!nome || !idade || !profissao) {
        alert("Preencha todos os campos.");
        return;
      }

      if (idade <= 0) {
        alert("A idade não pode ser menor que 0");
        return;
      }

      axios.post('http://localhost/controller/index.php', {
        action: 'cadastrar',
        nome,
        idade,
        profissao
      })
        .then(response => {
          if (response.data.message) {
            alert(response.data.message);
            this.getPessoas();
            this.form.nome = "";
            this.form.idade = "";
            this.form.profissao = "";
          }
        })
        .catch(error => {
          console.error('Erro ao cadastrar pessoa:', error);
          alert("Erro ao cadastrar pessoa.");
        });
    },

  }
};
</script>