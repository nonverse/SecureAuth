import axios from 'axios';
import auth from "./auth";

class user {
    constructor() {

        // API target endpoints
        this.url = `${(process.env.MIX_DEV === 'true') ? 'http' : 'https'}://${process.env.MIX_API_SERVER}/`;

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

    async activate(data) {
        return await axios.post(`${this.url}user/activate`, data)
    }

    // Create a new user
    async create(data) {
        //console.log(data)
        return await axios.post(`${this.url}user`, data)
    }

}

export default new user()
