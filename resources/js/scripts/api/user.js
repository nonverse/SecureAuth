import axios from 'axios';

class user {
    constructor() {

        // API target endpoint
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    // Create a new user
    async create(data) {
        //console.log(data)
        await axios.post(
            `${this.url}auth/create-new-user`, data)
            .then((response) => {
                console.log(response.data.data)
                return response.data.data
            }).catch((e) => {
                console.log(e)
                return false
            })
    }
}

export default new user()
