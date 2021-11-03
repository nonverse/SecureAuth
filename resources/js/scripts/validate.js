import axios from "axios";

class validate {
    constructor() {

        // Variables
        this.emailInvalid = true

        // API target endpoint
        this.url = 'http://api.nonverse.test/';

        // Config
        axios.defaults.withCredentials = true;
    }

    async validateNewEmail(email) {
        await axios.post(
            `${this.url}validator/validate-new-email`,
            {
                email: email,
            }
        ).then(() => {
            this.emailInvalid = false
        }).catch(() => {
            this.emailInvalid = true
        })
    }

    email(value) {
        let error;
        if (!value) {
            error = "An email is required"
        } else if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value)) {
            error = "Email is invalid"
        }
        return error
    }

    require(value) {
        let error;
        if (!value) {
            error = "This field is required"
        }
        return error
    }
}

export default new validate()
