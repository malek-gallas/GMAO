<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Liste des intervenants</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">
                                Ajouter</button>                   
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Login</th>
                                    <th>type</th>
                                </tr>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td>{{user.matricule}}</td>
                                    <td>{{user.name}}</td>
                                    <td>{{user.surname}}</td>
                                    <td>{{user.username}}</td>
                                    <td>{{user.type}}</td>   
                                    <td>
                                        <a href="#" @click="editModal(user)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteUser(user.id)">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">

                    </div>

                </div>
            </div>
        </div>

            <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editmode" id="modal-label">Ajouter</h5>
                        <h5 class="modal-title" v-show="editmode" id="modal-label">Modifier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.matricule" type="text" name="matricule"
                                placeholder="Matricule"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('matricule') }">
                                <div v-if="form.errors.has('matricule')" v-html="form.errors.get('matricule')"></div>
                            </div>
                            
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name"
                                placeholder="Nom"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <div v-if="form.errors.has('name')" v-html="form.errors.get('matricule')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.surname" type="text" name="surname"
                                placeholder="Prénom"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('surname') }">
                                <div v-if="form.errors.has('surname')" v-html="form.errors.get('matricule')"></div>
                                </div>


                            <div class="form-group">
                                <input v-model="form.username" type="text" name="username"
                                placeholder="Login"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                                <div v-if="form.errors.has('username')" v-html="form.errors.get('matricule')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password"
                                placeholder="Mot de passe"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <div v-if="form.errors.has('passwor')" v-html="form.errors.get('matricule')"></div>
                            </div>

                            <div class="form-group">
                                <select v-model="form.type" type="text" name="type" id="type"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Choisir la fonction</option>
                                    <option value="admin">Admin</option>
                                    <option value="technicien">Technicien</option>
                                    <option value="chef de service">Chef de service</option>
                                </select>
                                <div v-if="form.errors.has('type')" v-html="form.errors.get('type')"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" :disabled="form.busy">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success" :disabled="form.busy">Modifier</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary" :disabled="form.busy">Ajouter</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>   
    </div>
</template>

<script>
import Form from 'vform'
// Create custom event bus
import mitt from 'mitt';
const emitter = mitt()
    export default {
        data(){
            return{
                editmode: false,
                users :{},
                form: new Form({
                    id:'',
                    matricule: '',
                    name:'',
                    surname:'',
                    username:'',
                    password:'',
                    type: ''
                })
            }
        },
        methods: {
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#modal').modal('show');
            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#modal').modal('show');
                this.form.fill(user);
            },
            loadUsers(){
                axios.get("api/user").then(({ data }) => (this.users = data));  
            },
            createUser(){
                this.$Progress.start();
                this.form.post('/api/user').then(()=>{
                emitter.emit('refresh');
                $('#modal').modal('hide');
                const Toast = this.$swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', this.$swal.stopTimer)
                        toast.addEventListener('mouseleave', this.$swal.resumeTimer)
                    }
                })
                Toast.fire({
                    icon: 'success',
                    title: 'Signed in successfully'
                })
                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                })
            },
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then(() => {
                    $('#modal').modal('hide');
                    this.$swal.fire(
                        'Modifié!',
                        'Informations modifiés!',
                        'success'
                        )
                        this.$Progress.finish();
                        emitter.emit('refresh');
                })
                .catch(() => {
                    this.$Progress.fail();
                });
            },
            deleteUser(id){
                this.$swal.fire({
                    title: 'Voulez vous vraiment supprimer cet intervenant ?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, Supprimer!',
                    }).then((result) => {
                         if (result.value) {
                                this.form.delete('api/user/'+id).then(()=>{
                                        this.$swal.fire(
                                        'Supprimé!',
                                        'Element supprimé.',
                                        'success'
                                        )
                                        emitter.emit('refresh');
                                }).catch(()=> {
                                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                                });
                         }
                    })
            },
        },
        created() {
            this.loadUsers();
            emitter.on('refresh',()=>{
                this.loadUsers();
            });
        }
    }
</script>
