import axios from "axios";

class auth {
    constructor() {

        // Auth URI
        this.url = `${(process.env.MIX_DEV === 'true') ? 'http' : 'https'}://${process.env.MIX_AUTH_SERVER}/`

        // Variables
        const query = new URLSearchParams(window.location.search)
        this.host = query.has('host') ? query.get('host') : ''
        this.resource = query.has('resource') ? query.get('resource') : ''

        // Config
        axios.defaults.withCredentials = true;
    }

    async verifyEmail(email) {
        return await axios.post(
            `${this.url}login/verify-email`,
            {
                email: email,
            }
        )
    }

    async login(credentials) {
        return await axios.post(`${this.url}login?host=${this.host}&resource=${this.resource}`, credentials)
    }

    async twofactor(token, code, recovery_token) {

        let data = {
            code: code
        }
        if (recovery_token) {
            data = {
                recovery_token: recovery_token
            }
        }

        return axios.post(
            `${this.url}login/two-factor?host=${this.host}&resource=${this.resource}`,
            {
                auth_token: token,
                ...data
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

    async reset(data) {
        const query = new URLSearchParams(window.location.search)
        return axios.post(
            `${this.url}reset`, {
                password: data.password,
                password_confirmation: data.password_confirmation,
                email: query.get('email'),
                token: query.get('token')
            }
        )
    }
}

export default new auth();
