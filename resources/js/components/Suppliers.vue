<template>
    <div class="container">

        <div class="row mt-5" v-if="$gate.isManager()">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Liste des fournisseurs</h3>
                        <div class="card-tools">
                            <button class="btn btn-success mr-3" @click="newModal">Ajouter</button>       
                            <button class="btn btn-info" @click="exportExcel">Exporter</button>    
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Nom</th>
                                    <th>Addresse</th>
                                    <th>Téléphone</th>
                                    <th>Fax</th>
                                    <th>Email</th>
                                </tr>
                                <tr v-for="supplier in suppliers.data" :key="supplier.id">
                                    <td>{{supplier.name}}</td>
                                    <td>{{supplier.address}}</td>
                                    <td>{{supplier.telephone}}</td>
                                    <td>{{supplier.fax}}</td>
                                    <td>{{supplier.email}}</td>   
                                    <td>
                                        <a href="#" @click="editModal(supplier)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteSupplier(supplier.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <Bootstrap5Pagination :data="suppliers" @pagination-change-page="getResults"/>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="row mt-5" v-if="!$gate.isManager()">
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

                    <form @submit.prevent="editmode ? updateSupplier() : createSupplier()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" id="name"
                                placeholder="Nom"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <div v-if="form.errors.has('name')" v-html="form.errors.get('name')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.address" type="text" name="address" id="address"
                                placeholder="Addresse"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('address') }">
                                <div v-if="form.errors.has('address')" v-html="form.errors.get('address')"></div>
                            </div>
                            
                            <div class="form-group">
                                <input v-model="form.telephone" type="number" name="telephone" id="telephone"
                                placeholder="Téléphone" autocomplete="telephone"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('telephone') }">
                                <div v-if="form.errors.has('telephone')" v-html="form.errors.get('telephone')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.fax" type="number" name="fax" id="fax"
                                placeholder="Fax" autocomplete="fax"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('fax') }">
                                <div v-if="form.errors.has('fax')" v-html="form.errors.get('fax')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.email" type="email" name="email" id="email"
                                placeholder="Email" autocomplete="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <div v-if="form.errors.has('email')" v-html="form.errors.get('email')"></div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" :disabled="form.busy">Fermer</button>
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
                name: 'Liste des fournisseurs',
                editmode: false,
                suppliers :{},
                form: new Form({
                    //id: '',
                    name:'',
                    address: '',
                    telephone:'',
                    fax:'',
                    email:'',
                })
            }
        },
        methods: {
            // Create supplier modal
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#modal').modal('show');
            },
            // Updata supplier modal
            editModal(supplier){
                this.editmode = true;
                this.form.reset();
                $('#modal').modal('show');
                this.form.id = supplier.id;
                this.form.fill(supplier);
            },
            // Get paginated results
            getResults(page = 1) {
                axios.get('api/supplier?page=/'+page)
                    .then(response => {
                        this.suppliers = response.data;
                    });
            },
            // Create
            createSupplier(){
                this.$Progress.start();
                this.form.post('api/supplier').then(()=>{
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
                    title: 'Fournisseur ajouté'
                })
                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                })
            },
            // Read
            loadSuppliers(){
                if(this.$gate.isManager()){
                    axios.get("api/supplier").then(({ data }) => (this.suppliers = data)); 
                } 
            },
            // Update
            updateSupplier(){
                this.$Progress.start();
                this.form.put('api/supplier/'+this.form.id)
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
            deleteSupplier(id){
                this.$swal.fire({
                    title: 'Voulez vous vraiment supprimer cet fournisseur ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, Supprimer!',
                    }).then((result) => {
                         if (result.value) {
                                this.form.delete('api/supplier/'+id).then(()=>{
                                        this.$swal.fire(
                                        'Supprimé!',
                                        'Fournisseur supprimé.',
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
                arr = Object.values(this.suppliers.data);
                let data = this.$xlsx.utils.json_to_sheet(arr);
                const workbook = this.$xlsx.utils.book_new();
                const filename = this.name;
                this.$xlsx.utils.book_append_sheet(workbook, data, filename);
                this.$xlsx.writeFile(workbook, `${filename}.xlsx`);
            },
        },
        created() {
            // Reload Suppliers when vue component is created
            this.loadSuppliers();
            // Reload Suppliers when data has changed
            this.$emitter.on('refresh',()=>{
                this.loadSuppliers();
            });
            // Reload Suppliers when user is searching
            this.$emitter.on('searching', (query) => {
                axios.get('api/supplier/' + query)
                    .then(({ data }) => {
                        this.suppliers = data;
                    })
                    .catch(() => {
                        console.log("Search is not available");
                    });
            });
        }
    }
</script>
