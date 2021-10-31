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
            `${this.url}auth/test`,
            {
                email: email,
            }
        ).then((r) => {
            this.emailUsed = true
        }).catch((e) => {
            this.emailUsed = false
        })
    }

    // Create a new user
    async create(data) {
        await axios.post(
            `${this.url}auth/create-new-user`,
            {
                email: data.email,
                username: data.username,
                name_first: data.firstname,
                name_last: data.lastname,
                password: data.password,
                password_confirmation: data.password_confirmation
            }
        ).then(() => {
            return true
        }).catch((e) => {
            console.log(e)
            return false
        })
    }
}

export default new user()
