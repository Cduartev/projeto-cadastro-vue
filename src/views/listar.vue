<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-dark text-white text-center">
            <h4 class="mb-0">Lista de Usuários</h4>
          </div>
          <div class="card-body bg-dark p-3">
            <div v-if="editIndex !== null" class="mb-4 p-3 border rounded bg-dark text-light">
              <h5 class="mb-3">Editar Usuário</h5>
              <div class="mb-2">
                <label class="form-label">Nome:</label>
                <input type="text" class="form-control" v-model="form.nome" />
              </div>
              <div class="mb-2">
                <label class="form-label">Idade:</label>
                <input type="number" class="form-control" v-model="form.idade" />
              </div>
              <div class="mb-2">
                <label class="form-label">Profissão:</label>
                <input type="text" class="form-control" v-model="form.profissao" />
              </div>
              <div class="mt-3">
                <button class="btn btn-success me-2" @click="salvarEdicao">Salvar</button>
                <button class="btn btn-secondary" @click="cancelarEdicao">Cancelar</button>
              </div>
            </div>

       
            <div v-if="usuarios.length === 0" class="text-center text-light">
              Nenhum usuário encontrado.
            </div>

            <div v-else class="user-card bg-dark text-light mb-3 p-3 border rounded shadow-sm"
              v-for="(item, index) in usuarios" :key="index">
              <h5 class="fw-bold mb-2">{{ item.nome }}</h5>
              <p class="mb-1"><strong>Idade:</strong> {{ item.idade }} anos</p>
              <p class="mb-0"><strong>Profissão:</strong> {{ item.profissao }}</p>
              <div class="mx-1 mt-2">
                <button type="button" class="btn btn-warning text-dark" @click="editar(index)">
                  Editar<i class="bi bi-pencil mx-1"></i>
                </button>
                <button type="button" class="btn btn-danger text-dark mx-2" @click="excluir(index)">
                  Excluir<i class="bi bi-trash mx-1"></i>
                </button>
                <button class="btn btn-primary" @click="ver_familia(item.id)">
                  Ver Família<i class="bi bi-people-fill mx-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

       
        <div v-if="familiares.length > 0 || idUsuarioSelecionado" class="card shadow-sm mb-4">
          <div class="card-header bg-secondary text-white text-center">
            <h5 class="mb-0">Familiares do Usuário</h5>
          </div>
          <div class="card-body bg-dark text-light">
            <div v-if="familiares.length === 0" class="text-center text-muted">
              Nenhum familiar cadastrado.
            </div>

            <div v-else class="mb-3 bg-dark" v-for="(familiar, index) in familiares" :key="familiar.id">
              <div class="p-3 border rounded mb-2 bg-dark text-light">
                <h6 class="fw-bold">{{ familiar.nome }}</h6>
                <p><strong>Parentesco:</strong> {{ familiar.parentesco }}</p>
                <p><strong>Idade:{{ familiar.idade }}</strong></p>
              </div>
            </div>

            <hr />


            <h6 class="mb-3">Cadastrar novo familiar</h6>
            <div class="mb-2 bg-dark text-light">
              <label class="form-label">Nome:</label>
              <input type="text" class="form-control" v-model="familiaForm.nome" />
            </div>
            <div class="mb-3">
              <label class="form-label">Idade:</label>
              <input type="number" class="form-control" v-model="familiaForm.idade" />
            </div>

            <div class="mb-3">
              <label class="form-label">Parentesco:</label>
              <input type="text" class="form-control" v-model="familiaForm.parentesco" />
            </div>
            <div>
              <button class="btn btn-success me-2" @click="salvarFamiliar">Salvar Familiar</button>
              <button class="btn btn-danger" @click="cancelarCadastroFamiliar">Fechar/Cancelar</button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'sobre',
  data() {
    return {
      usuarios: [],
      form: {
        nome: '',
        idade: '',
        profissao: '',
        id: null
      },
      editIndex: null,
      familiares: [],
      idUsuarioSelecionado: null,
      familiaForm: {
        nome: '',
        parentesco: '',
        idade: ''
      }

    };
  },

  mounted() {
    this.getPessoas();
  },

  methods: {
    getPessoas() {
      axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
        action: 'listar'
      })
        .then(response => {
          this.usuarios = response.data;
        })
        .catch(error => {
          this.$toast.error('Erro ao buscar dados:', error);
        });
    },

    editar(index) {
      const usuario = this.usuarios[index];
      this.form = {
        nome: usuario.nome,
        idade: usuario.idade,
        profissao: usuario.profissao,
        id: usuario.id
      };
      this.editIndex = index;
    },

    salvarEdicao() {
      const { nome, idade, profissao, id } = this.form;
      if (!nome || !idade || !profissao) {
        this.$toast.info("Preencha todos os campos.");
        return;
      }

      axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
        action: 'editar',
        id,
        nome,
        idade,
        profissao
      })
        .then(response => {
          if (response.data.success) {
            this.getPessoas();
            this.form = { nome: '', idade: '', profissao: '', id: null };
            this.editIndex = null;
          } else {
            this.$toast.error("Erro ao salvar edição.");
          }
        })
        .catch(error => {
          this.$toast.error("Erro ao salvar edição:", error);
        });
    },

    cancelarEdicao() {
      this.form = { nome: '', idade: '', profissao: '', id: null };
      this.editIndex = null;
    },

    excluir(index) {
      const usuario = this.usuarios[index];
      if (!confirm(`Excluir o usuário ${usuario.nome}?`)) {
        return;
      }

      axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
        action: 'excluir',
        id: usuario.id
      })
        .then(response => {
          if (response.data.success) {
            this.usuarios.splice(index, 1);
          } else {
            this.$toast.error("Erro ao excluir usuário.");
          }
        })
        .catch(error => {
          this.$toast.error("Erro ao excluir usuário:", error);
        });
    },

    ver_familia(id) {
      this.idUsuarioSelecionado = id;

      axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
        action: 'listar_familiares',
        id_pessoa: id
      })
        .then(response => {
          if (response.data.success) {
            this.familiares = response.data.familiares;
          } else {
            this.$toast.error("Erro ao buscar familiares.");
          }
        })
        .catch(error => {
          this.$toast.error("Erro ao buscar familiares:", error);
        });
    },

    salvarFamiliar() {
      const { nome, idade, parentesco } = this.familiaForm;

      if (!nome || !idade || !parentesco) {
        this.$toast.info("Preencha todos os campos do familiar.");
        return;
      }

      axios.post('http://localhost/projeto-cadastro-vue/controller/index.php', {
        action: 'cadastrar_familiar',
        id_pessoa: this.idUsuarioSelecionado,
        nome,
        parentesco,
        idade,
      })
        .then(response => {
          if (response.data.success) {
            this.familiaForm = { nome: '', idade: '', parentesco: '' };
            this.ver_familia(this.idUsuarioSelecionado);
          } else {
            this.$toast.error("Erro ao salvar familiar.");
          }
        })
        .catch(error => {
          this.$toast.error("Erro ao salvar familiar:", error);
        });
    },

    cancelarCadastroFamiliar() {
      this.familiaForm = { nome: '',idade: '', parentesco: '' };
      this.familiares = [];
      this.idUsuarioSelecionado = null;
    }

  }
};
</script>
