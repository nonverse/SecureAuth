import axios from 'axios';

class user {
    constructor() {

        // API target endpoints
        this.url = 'http://api.nonverse.test/';
        this.auth = 'http://auth.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    async verifyEmail(email) {
        return await axios.post(
            `${this.auth}api/verify-user-email`,
            {
                email: email,
            }
        )
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
