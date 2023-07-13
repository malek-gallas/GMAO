import axios from 'axios';
import { createStore } from 'vuex'

const store = createStore({
    state:{
        suppliers: [],
    },
    actions: {
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
    },
    mutations:{
        SET_SUPPLIERS (state, suppliers){
            state.suppliers=suppliers
        },
    }
})

export default store;