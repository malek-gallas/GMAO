<template>
    <div class="container">

        <div class="row mt-5" v-if="$gate.isManager()">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Liste des machines</h3>
                        <div class="card-tools">
                            <button class="btn btn-success mr-3" @click="newModal">Ajouter</button>       
                            <button class="btn btn-info" @click="exportExcel">Exporter</button>    
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Série</th>
                                    <th>Section</th>
                                    <th>Unité</th>
                                    <th>Date d'acquisation</th>
                                    <th>Fournisseur</th>
                                </tr>
                                <tr v-for="machine in machines.data" :key="machine.id">
                                    <td>{{machine.code}}</td>
                                    <td>{{machine.name}}</td>
                                    <td>{{machine.type}}</td>
                                    <td>{{machine.serie}}</td>
                                    <td>{{machine.section}}</td>   
                                    <td>{{machine.unit}}</td>
                                    <td>{{machine.purchase_date}}</td>
                                    <td>{{machine.supplier}}</td>
                                    
                                    <td>
                                        <a href="#" @click="editModal(machine)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteMachine(machine.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <Bootstrap5Pagination :data="machines" @pagination-change-page="getResults"/>
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

                    <form @submit.prevent="editmode ? updateMachine() : createMachine()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.code" type="text" name="code" id="code"
                                placeholder="Code"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('code') }">
                                <div v-if="form.errors.has('code')" v-html="form.errors.get('code')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" id="name"
                                placeholder="Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <div v-if="form.errors.has('name')" v-html="form.errors.get('name')"></div>
                            </div>
                            
                            <div class="form-group">
                                <input v-model="form.type" type="text" name="type" id="type"
                                placeholder="Type" autocomplete="type"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <div v-if="form.errors.has('type')" v-html="form.errors.get('type')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.serie" type="text" name="serie" id="serie"
                                placeholder="Série" autocomplete="serie"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('serie') }">
                                <div v-if="form.errors.has('serie')" v-html="form.errors.get('serie')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.section" name="section" id="section"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('section') }">
                                <option value="">Choisir la section:</option>
                                <option value="Machine">Machine</option>
                                <option value="Mécanique">Mécanique</option>
                                <option value="Atelier spécial">Atelier spécial</option>
                                <option value="Chromage et pulvérisation">Chromage et pulvérisation</option>
                            </select>
                            <div v-if="form.errors.has('section')" v-html="form.errors.get('section')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.unit" name="unit" id="unit"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('unit') }">
                                <option value="">Choisir l'unité:</option>
                                <option value="Meubles modulaires">Meubles modulaires</option>
                                <option value="Les mètiers">Les mètiers</option>
                                <option value="Meubles massifs">Meubles massifs</option>
                                <option value="Panoverre">Panoverre</option>
                                <option value="Métal design">Métal design</option>
                                <option value="Poly-meuble">Poly-meuble</option>
                                <option value="L'art de salon">L'art de salon</option>
                            </select>
                            <div v-if="form.errors.has('unit')" v-html="form.errors.get('unit')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.supplier" name="supplier" id="supplier"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('supplier') }">
                                <option value="">Choisir le fournisseur:</option>
                                <option v-for="(supplier, id) in suppliers" v-bind:key="id" 
                                    v-bind:value="supplier.name">
                                    {{supplier.name}}
                                    </option>
                            </select>
                            <div v-if="form.errors.has('supplier')" v-html="form.errors.get('supplier')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.purchase_date" type="date" name="purchase_date" id="purchase_date"
                                placeholder="Date d'achat" autocomplete="purchase_date"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('purchase_date') }">
                                <div v-if="form.errors.has('purchase_date')" v-html="form.errors.get('purchase_date')"></div>
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
import {mapState} from 'vuex'
    export default {
        data(){
            return{
                name: 'Liste des machines',
                editmode: false,
                machines :{},
                form: new Form({
                    id:'',
                    code:'',
                    name: '',
                    type:'',
                    serie:'',
                    section:'non-définie',
                    unit:'non-définie',
                    supplier:'non-définie',
                    purchase_date:''  
                })
            }
        },
        methods: {
            // Create machine modal
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#modal').modal('show');
            },
            // Updata machine modal
            editModal(machine){
                this.editmode = true;
                this.form.reset();
                $('#modal').modal('show');
                this.form.fill(machine);
            },
            // Get paginated results
            getResults(page = 1) {
                axios.get('api/machine?page='+page)
                    .then(response => {
                        this.machines = response.data;
                    });
            },
            // Create
            createMachine(){
                this.$Progress.start();
                this.form.post('api/machine').then(()=>{
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
                    title: 'Machine ajouté'
                })
                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                })
            },
            // Read
            loadMachines(){
                if(this.$gate.isManager()){
                    axios.get("api/machine").then(({ data }) => (this.machines = data)); 
                } 
            },
            // Update
            updateMachine(){
                this.$Progress.start();
                this.form.put('api/machine/'+this.form.id)
                .then(() => {
                    $('#modal').modal('hide');
                    this.$swal.fire(
                        'Modifié!',
                        'Informations modifiées!',
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
            deleteMachine(id){
                this.$swal.fire({
                    title: 'Voulez vous vraiment supprimer cet machine ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, Supprimer!',
                    }).then((result) => {
                         if (result.value) {
                                this.form.delete('api/machine/'+id).then(()=>{
                                        this.$swal.fire(
                                        'Supprimé!',
                                        'Machine supprimé.',
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
                arr = Object.values(this.machines.data);
                let data = this.$xlsx.utils.json_to_sheet(arr);
                const workbook = this.$xlsx.utils.book_new();
                const filename = this.name;
                this.$xlsx.utils.book_append_sheet(workbook, data, filename);
                this.$xlsx.writeFile(workbook, `${filename}.xlsx`);
            },
        },
        
        mounted() {
            this.$store.dispatch('loadSuppliers')
        },
        computed: {
            ...mapState([
                'suppliers'
            ])
        },
        
        created() {
            // Reload machines when vue component is created
            this.loadMachines();
            // Reload machines when data has changed
            this.$emitter.on('refresh',()=>{
                this.loadMachines();
            });
            // Reload machines when user is searching
            this.$emitter.on('searching', (query) => {
                axios.get('api/machine/' + query)
                    .then(({ data }) => {
                        this.machines = data;
                    })
                    .catch(() => {
                        console.log("Search is not available");
                    });
            });
        }
    }
</script>
