import axios from 'axios';
import { createStore } from 'vuex'

const store = createStore({
    state:{
        workers: [],
        suppliers: [],
        machines: [],
        preventions: [],
        corrections: []
    },
    actions: {
        loadWorkers ({commit}){
            axios.get("api/user")
            .then(data => {
                console.log(data.data)
                let workers= data.data.data
                commit('SET_WORKERS',workers)
            })
            .catch(error => {
                console.log(error)
            })
        },
        loadSuppliers ({commit}){
            axios.get("api/supplier")
            .then(data => {
                console.log(data.data)
                let suppliers= data.data.data
                commit('SET_SUPPLIERS',suppliers)
            })
            .catch(error => {
                console.log(error)
            })
        },
        loadMachines ({commit}){
            axios.get("api/machine")
            .then(data => {
                console.log(data.data)
                let machines= data.data.data
                commit('SET_MACHINES',machines)
            })
            .catch(error => {
                console.log(error)
            })
        },
        loadPreventions ({commit}){
            axios.get("api/prevention")
            .then(data => {
                console.log(data.data)
                let preventions= data.data.data
                commit('SET_PREVENTIONS',preventions)
            })
            .catch(error => {
                console.log(error)
            })
        },
        loadCorrections({commit}){
            axios.get("api/correction")
            .then(data => {
                console.log(data.data)
                let corrections= data.data.data
                commit('SET_CORRECTIONS',corrections)
            })
            .catch(error => {
                console.log(error)
            })
        },
    },
    mutations:{
        SET_WORKERS (state, workers){
            state.workers=workers
        },
        SET_SUPPLIERS (state, suppliers){
            state.suppliers=suppliers
        },
        SET_MACHINES (state, machines){
            state.machines=machines
        },
        SET_PREVENTIONS (state, preventions){
            state.preventions=preventions
        },
        SET_CORRECTIONS (state, corrections){
            state.corrections=corrections
        }
    }
})

export default store;