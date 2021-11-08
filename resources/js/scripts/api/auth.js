import axios from "axios";

class auth {
    constructor() {

        // Auth URI
        this.url = 'http://auth.nonverse.test/'

        // Variables
        const query = new URLSearchParams(window.location.search)
        this.host = query.has('host') ? query.get('host') : ''
        this.resource = query.has('resource') ? query.get('resource') : ''

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
        return await axios.post(`${this.url}login?host=${this.host}&resource=${this.resource}`, credentials)
    }

    async twofactor(token, code) {
        return axios.post(
            `${this.url}login/two-factor?host=${this.host}&resource=${this.resource}`,
            {
                auth_token: token,
                code: code
            }
        )
    }

    async forgot(email) {
        return await axios.post(
            `${this.url}forgot`,
            {
                email: email
            }
        )
    }
}

export default new auth();
