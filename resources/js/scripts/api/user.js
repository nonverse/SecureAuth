import axios from 'axios';

class user {
    constructor() {

        // Variables
        this.emailUsed = ''

        // API target endpoint
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    async verifyEmail(email) {
        await axios.post(
            `${this.url}auth/verify/validate-user-email`,
            {
                email: email,
            }
        ).then(() => {
            this.emailUsed = false
        }).catch(() => {
            this.emailUsed = true
        })
    }

    // Create a new user
    async create(data) {
        console.log(data)
        await axios.post(
            `${this.url}auth/create-new-user`, data).then(() => {
            return true
        }).catch((e) => {
            console.log(e)
            return false
        })
    }
}

export default new user()
