import axios from 'axios';
import auth from "./auth";

class user {
    constructor() {

        // API target endpoints
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    // Get stored user email and name if exists
    async getCookie() {
        return await axios.get(`${auth.url}api/user/cookie`)
    }

    async deleteCookie() {
        return await axios.post(`${auth.url}api/user/cookie`)
    }

    // Create a new user
    async create(data) {
        //console.log(data)
        return await axios.post(`${this.url}user`, data)
    }
}

export default new user()
