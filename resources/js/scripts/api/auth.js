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
            `${this.url}api/user`,
            {
                email: email,
            }
        )
    }

    async login(credentials) {
        const query = new URLSearchParams(window.location.search)
        const host = query.has('host') ? query.get('host') : ''
        const resource = query.has('resource') ? query.get('resource') : ''
        return await axios.post(`${this.url}login?host=${host}&resource=${resource}`, credentials)
    }

    async twofactor(token, code) {
        return axios.post(
            `${this.url}login/two-factor`,
            {
                auth_token: token,
                code: code
            }
        )
    }
}

export default new auth();
