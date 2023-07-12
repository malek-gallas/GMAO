<template>
    <div class="container">

        <div class="row mt-5" v-if="$gate.isAdmin()">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Liste des utilisateurs</h3>
                        <div class="card-tools">
                            <button class="btn btn-success mr-3" @click="newModal">Ajouter</button>       
                            <button class="btn btn-info" @click="exportExcel">Exporter</button>    
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                </tr>
                                <tr v-for="user in users.data" :key="user.id">
                                    <td>{{user.employee_id}}</td>
                                    <td>{{user.first_name}}</td>
                                    <td>{{user.last_name}}</td>
                                    <td>{{user.email}}</td>
                                    <td>{{user.role}}</td>   
                                    <td>
                                        <a href="#" @click="editModal(user)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteUser(user.id)">
                                            <i class="fa fa-trash red"></i>
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
                                <input v-model="form.employee_id" type="text" name="employee_id" id="employee_id"
                                placeholder="Matricule"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('employee_id') }">
                                <div v-if="form.errors.has('employee_id')" v-html="form.errors.get('employee_id')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.first_name" type="text" name="first_name" id="first_name"
                                placeholder="Prénom"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }">
                                <div v-if="form.errors.has('first_name')" v-html="form.errors.get('first_name')"></div>
                            </div>
                            
                            <div class="form-group">
                                <input v-model="form.last_name" type="text" name="last_name" id="last_name"
                                placeholder="Nom" autocomplete="last_name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }">
                                <div v-if="form.errors.has('last_name')" v-html="form.errors.get('last_name')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.role" name="role" id="role"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('role') }">
                                <option value="">Choisir la fonction</option>
                                <option value="Administrateur">Administrateur</option>
                                <option value="Chef de service">Chef de service</option>
                                <option value="Technicien">Technicien</option>
                            </select>
                            <div v-if="form.errors.has('role')" v-html="form.errors.get('role')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.email" type="email" name="email" id="email"
                                placeholder="Email" autocomplete="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <div v-if="form.errors.has('email')" v-html="form.errors.get('email')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password" id="password"
                                placeholder="Mot de passe"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <div v-if="form.errors.has('password')" v-html="form.errors.get('password')"></div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" :disabled="form.busy">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success" :disabled="form.busy">Modifier</button>
                            <button v-show="!editmode" type="submit" class="btn btn-success" :disabled="form.busy">Ajouter</button>
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
                    employee_id: '',
                    first_name:'',
                    last_name:'',
                    role:'',
                    email:'',
                    password: ''
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
                                this.form.delete('api/user'+id).then(()=>{
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
