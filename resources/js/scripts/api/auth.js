import axios from "axios";

class auth {
    constructor() {

        // Auth URI
        this.url = 'http://auth.nonverse.test/'

        // Config
        axios.defaults.withCredentials = true;
    }

    async verifyEmail(email) {
        return await axios.post(
            `${this.url}api/verify-user-email`,
            {
                email: email,
            }
        )
    }

    async login(credentials) {
        return await axios.post(`${this.url}login`, credentials)
    }
}

export default new auth();
