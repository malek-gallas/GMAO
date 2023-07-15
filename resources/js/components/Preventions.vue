<template>
    <div class="container">

        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Interventions préventives</h3>
                        <div class="card-tools" v-if="$gate.isManager()">
                            <button class="btn btn-success mr-3" @click="newModal">Ajouter</button>       
                            <button class="btn btn-info" @click="exportExcel">Exporter</button>    
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>Tâche</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Intervenants</th>
                            <th>Machine</th>
                            <th>Pièces</th>
                            <th>Priorité</th>
                            <th>Etat</th>
                        </tr>
                        <tr v-for="prevention in preventions.data" :key="prevention.id">
                            <td>{{ prevention.task }}</td>
                            <td class="single-line">{{ prevention.start_date }}</td>
                            <td class="single-line">{{ prevention.end_date }}</td>
                            <td>
                                <span v-for="(worker, index) in prevention.workers" :key="index">
                                    {{ worker.name }}
                                    <span v-if="index !== prevention.workers.length - 1">, </span>
                                </span>
                            </td>
                            <td>{{ prevention.machine }}</td>
                            <td>{{ prevention.parts }}</td>
                            <td :style="{ color: getColorForPriority(prevention.priority) }">{{ prevention.priority }}</td>
                            <td>
                            <select v-model="prevention.state" @change="updatePreventionState(prevention)">
                                <option value="A Faire">A Faire</option>
                                <option value="En Cours">En Cours</option>
                                <option value="Terminee">Terminee</option>
                            </select>
                            </td>

                            <td class="single-line" v-if="$gate.isManager()">
                            <a href="#" @click="editModal(prevention)">
                                <i class="fa fa-edit"></i>
                            </a>
                            /
                            <a href="#" @click="deletePrevention(prevention.id)">
                                <i class="fa fa-trash red"></i>
                            </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="card-footer">
                        <Bootstrap5Pagination :data="preventions" @pagination-change-page="getResults"/>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="row mt-5" v-if="!$gate.isManager() && !$gate.isWorker()">
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

                    <form @submit.prevent="editmode ? updatePrevention() : createPrevention()">
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <input v-model="form.task" type="text" name="task" id="task"
                                placeholder="Tâche"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('task') }">
                                <div v-if="form.errors.has('task')" v-html="form.errors.get('task')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.start_date" type="date" name="start_date" id="start_date"
                                placeholder="Début" autocomplete="start_date"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('start_date') }">
                                <div v-if="form.errors.has('start_date')" v-html="form.errors.get('start_date')"></div>
                            </div>

                            <div class="form-group">
                                <input v-model="form.end_date" type="date" name="end_date" id="end_date"
                                placeholder="Fin" autocomplete="end_date"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('end_date') }">
                                <div v-if="form.errors.has('end_date')" v-html="form.errors.get('end_date')"></div>
                            </div>

                            <div class="form-group">
                                <VueMultiselect
                                    v-model="form.workers"
                                    :options="workersOptions"
                                    :multiple="true"
                                    :taggable="true"
                                    @tag="addTag"
                                    tag-placeholder="Intervenant X"
                                    placeholder="Choisir l'intervenant"
                                    label="name"
                                    track-by="employee_id"
                                ></VueMultiselect>
                                <div v-if="form.errors.has('workers')" v-html="form.errors.get('workers')"></div>
                                </div>

                            <div class="form-group">
                                    <select v-model="form.machine" type="text" name="machine" id="machine" 
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('machine') }">
                                        <option value="">Choisir la machine:</option>
                                        <option v-for="(machine, id) in machines" v-bind:key="id" 
                                        v-bind:value="machine.code+'-'+machine.name">
                                        {{machine.code}}-{{machine.name}} 
                                        </option>
                                    </select>
                                    <div v-if="form.errors.has('machine')" v-html="form.errors.get('machine')"></div>
                                </div>

                            <div class="form-group">
                                <input v-model="form.parts" type="text" name="parts" id="parts"
                                placeholder="Pièces"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('parts') }">
                                <div v-if="form.errors.has('parts')" v-html="form.errors.get('parts')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.priority" name="priority" id="priority"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('priority') }">
                                <option value="">Choisir la priorité :</option>
                                <option value="Haute">Haute</option>
                                <option value="Medium">Medium</option>
                                <option value="Faible">Faible</option>
                            </select>
                            <div v-if="form.errors.has('priority')" v-html="form.errors.get('priority')"></div>
                            </div>

                            <div class="form-group">
                            <select v-model="form.state" name="state" id="state"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('state') }">
                                <option value="">Choisir l'état :</option>
                                <option value="A Faire">A Faire</option>
                                <option value="En Cours">En Cours</option>
                                <option value="Terminee">Terminée</option>
                            </select>
                            <div v-if="form.errors.has('state')" v-html="form.errors.get('state')"></div>
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
                taggingOptions: [], // Initialize with an empty array or your existing data
                taggingSelected: [],
                name: 'Interventions préventives',
                editmode: false,
                preventions :{},
                form: new Form({
                    id:'',
                    task:'',
                    start_date:'',
                    end_date:'',
                    workers:'',
                    machine:'',
                    parts:'',   
                    priority:'',  
                    state:''
                })
            }
        },
        methods: {
            addTag (newTag) {
                const tag = {
                    name: newTag,
                    code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
                }
                this.taggingOptions.push(tag)
                this.taggingSelected.push(tag)
            },
            // Create prevention modal
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#modal').modal('show');
            },
            // Updata prevention modal
            editModal(prevention){
                this.editmode = true;
                this.form.reset();
                $('#modal').modal('show');
                this.form.fill(prevention);
            },
            // Get paginated results
            getResults(page = 1) {
                axios.get('api/prevention?page='+page)
                    .then(response => {
                        this.preventions = response.data;
                    });
            },
            // Create
            createPrevention(){
                this.$Progress.start();
                this.form.post('api/prevention').then(()=>{
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
                    title: 'Intervention préventive ajoutée'
                })
                    this.$Progress.finish();
                })
                .catch(()=>{
                    this.$Progress.fail();
                    this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                })
            },
            // Read
            loadPreventions(){
                if(this.$gate.isManager() || this.$gate.isWorker()){
                    axios.get("api/prevention").then(({ data }) => (this.preventions = data)); 
                } 
            },
            // Update
            updatePrevention(){
                this.$Progress.start();
                this.form.put('api/prevention/'+this.form.id)
                .then(() => {
                    $('#modal').modal('hide');
                    this.$swal.fire(
                        'Modifiée!',
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
            deletePrevention(id){
                this.$swal.fire({
                    title: 'Voulez vous vraiment supprimer cette intervention ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, Supprimer!',
                    }).then((result) => {
                         if (result.value) {
                                this.form.delete('api/prevention/'+id).then(()=>{
                                        this.$swal.fire(
                                        'Supprimée!',
                                        'Intervention préventive supprimée.',
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
                arr = Object.values(this.preventions.data);
                let data = this.$xlsx.utils.json_to_sheet(arr);
                const workbook = this.$xlsx.utils.book_new();
                const filename = this.name;
                this.$xlsx.utils.book_append_sheet(workbook, data, filename);
                this.$xlsx.writeFile(workbook, `${filename}.xlsx`);
            },
            updatePreventionState(prevention) {
                this.$Progress.start();
                axios.put(`api/prevention/state/${prevention.id}`, { state: prevention.state })
                .then(() => {
                $('#modal').modal('hide');
                this.$swal.fire('Modifiée!', 'Informations modifiées!', 'success');
                this.$Progress.finish();
                this.$emitter.emit('refresh');
                })
                .catch(() => {
                this.$Progress.fail();
                this.$swal.fire("Echec!", "Il y'a un problème.", "warning");
                });
            },
            getColorForPriority(priority) {
                if (priority === 'Haute') {
                    return 'red';
                } else if (priority === 'Faible') {
                    return 'green';
                } else if (priority === 'Medium') {
                    return 'orange';
                } else {
                    return ''; // Default color if priority value doesn't match
                }
            },
        },
        mounted() {
            this.$store.dispatch('loadWorkers'),
            this.$store.dispatch('loadMachines')
        },
        computed: {
            ...mapState([
                'workers',
                'machines'
            ]),
            workersOptions() {
                return this.$store.state.workers.map(worker => ({
                employee_id: worker.employee_id,
                name: `(${worker.employee_id}) ${worker.first_name} ${worker.last_name}`
                }));
            },
        },
        created() {
            // Reload preventions when vue component is created
            this.loadPreventions();
            // Reload preventions when data has changed
            this.$emitter.on('refresh',()=>{
                this.loadPreventions();
            });
            // Reload preventions when user is searching
            this.$emitter.on('searching', (query) => {
                axios.get('api/prevention/' + query)
                    .then(({ data }) => {
                        this.preventions = data;
                    })
                    .catch(() => {
                        console.log("Search is not available");
                    });
            });
        }
    }
</script>

<style>
    .single-line {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    }
</style>