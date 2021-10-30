import axios from 'axios';

class user {
    constructor() {

        // API target endpoint
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    // Create a new user
    create($data) {
        axios.post(
            `${this.url}auth/create-new-user`,
            {
                email: $data.email,
                username: $data.email,
                name_first: $data.firstname,
                name_last: $data.lastname,
                password: $data.password,
                password_confirmation: $data.password_confirmation
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
