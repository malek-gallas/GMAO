export default class Gate{

    constructor(user){
        this.user = user;
    }


    isAdmin(){
        return this.user.type === 'admin';
    }

    isChef(){
        return this.user.type === 'chef de service';
    }

    isTechnicien(){
        return this.user.type === 'technicien';
    }
}
