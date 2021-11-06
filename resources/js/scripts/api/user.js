import axios from 'axios';

class user {
    constructor() {

        // API target endpoints
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    // Create a new user
    async create(data) {
        //console.log(data)
        return await axios.post(`${this.url}user`, data)
    }
}

export default new user()
