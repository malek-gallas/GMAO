export default class Gate{

    constructor(user){
        this.user = user;
    }


    isAdmin(){
        return this.user.role === 'Administrateur';
    }

    isManager(){
        return this.user.role === 'Chef de service';
    }

    isWorker(){
        return this.user.role === 'Technicien';
    }
}
