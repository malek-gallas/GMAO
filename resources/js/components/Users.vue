<template>
    <div class="container">

        <div class="row mt-5" v-if="$gate.isAdmin()">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Liste des intervenants</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Ajouter</button>       
                            <button class="btn btn-info" @click="exportExcel">Export excel</button>    
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
                        <Bootstrap5Pagination :data="users" @pagination-change-page="getResults"/>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="row mt-5" v-if="!$gate.isAdmin()">
            <Error></Error>
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
                                <input v-model="form.matricule" type="text" name="matricule" id="matricule"
                                placeholder="Matricule"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('matricule') }">
                                <div v-if="form.errors.has('matricule')" v-html="form.errors.get('matricule')"></div>
                            </div>
                            
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" id="name"
                                placeholder="Nom" autocomplete="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <div v-if="form.errors.has('name')" v-html="form.errors.get('matricule')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.surname" type="text" name="surname" id="surname"
                                placeholder="Prénom"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('surname') }">
                                <div v-if="form.errors.has('surname')" v-html="form.errors.get('matricule')"></div>
                                </div>


                            <div class="form-group">
                                <input v-model="form.username" type="text" name="username" id="username"
                                placeholder="Login" autocomplete="username"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                                <div v-if="form.errors.has('username')" v-html="form.errors.get('matricule')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password" id="password"
                                placeholder="Mot de passe"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <div v-if="form.errors.has('passwor')" v-html="form.errors.get('matricule')"></div>
                            </div>

      

                            <div class="form-group">
                            <select v-model="form.type" name="type" id="type"
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
    export default {
        data(){
            return{
                name: 'Liste des utilisateurs',
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
            // Create user modal
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#modal').modal('show');
            },
            // Updata user modal
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#modal').modal('show');
                this.form.fill(user);
            },
            // Get paginated results
            getResults(page = 1) {
                axios.get('api/user?page='+page)
                    .then(response => {
                        this.users = response.data;
                    });
            },
            // Create
            createUser(){
                this.$Progress.start();
                this.form.post('api/user').then(()=>{
                this.$emitter.emit('refresh');
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
                    title: 'Utilisateur ajouté'
                })
                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                })
            },
            // Read
            loadUsers(){
                if(this.$gate.isAdmin()){
                    axios.get("api/user").then(({ data }) => (this.users = data)); 
                } 
            },
            // Update
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
                        this.$emitter.emit('refresh');
                })
                .catch(() => {
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                });
            },
            // Delete
            deleteUser(id){
                this.$swal.fire({
                    title: 'Voulez vous vraiment supprimer cet utilisateur ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, Supprimer!',
                    }).then((result) => {
                         if (result.value) {
                                this.form.delete('api/user/'+id).then(()=>{
                                        this.$swal.fire(
                                        'Supprimé!',
                                        'Utilisateur supprimé.',
                                        'success'
                                        )
                                        this.$emitter.emit('refresh');
                                }).catch(()=> {
                                    this.$Progress.fail();
                                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                                });
                         }
                    })
            },
            // Export Excel data
            exportExcel() {
                var arr = [];
                arr = Object.values(this.users.data);
                let data = this.$xlsx.utils.json_to_sheet(arr);
                const workbook = this.$xlsx.utils.book_new();
                const filename = this.name;
                this.$xlsx.utils.book_append_sheet(workbook, data, filename);
                this.$xlsx.writeFile(workbook, `${filename}.xlsx`);
            },
        },
        created() {
            // Reload Users when vue component is created
            this.loadUsers();
            // Reload Users when data has changed
            this.$emitter.on('refresh',()=>{
                this.loadUsers();
            });
            // Reload Users when user is searching
            this.$emitter.on('searching', (query) => {
                axios.get('api/user/' + query)
                    .then(({ data }) => {
                        this.users = data;
                    })
                    .catch(() => {
                        console.log("Search is not available");
                    });
            });
        }
    }
</script>
