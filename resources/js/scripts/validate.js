import axios from "axios";
import api from "./api/api";

class validate {
    constructor() {

        // API target endpoint
        this.url = `${api.url}validator`;

        // Config
        axios.defaults.withCredentials = true;
    }

    async validateNewEmail(email) {
        return await axios.post(
            `${this.url}user/email`,
            {
                email: email,
            }
        )
    }

    async validateNewUser(username) {
        return await axios.post(
            `${this.url}user`,
            {
                username: username,
            }
        )
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

    require(value, min, max) {
        let error;
        if (!value) {
            error = "This field is required"
        }
        if (min) {
            if (value.length < min) {
                error = `At least ${min} characters are required`
            }
        }
        if (max) {
            if (value.length > max) {
                error = `No more than ${max} characters are allowed`
            }
        }
        return error
    }

    confirmation(value, compare) {
        let error
        if (!value) {
            error = 'Confirmation is required'
        }
        if (value !== compare) {
            error = 'Confirmation does not match'
        }
        return error
    }

    otp(value) {
        let error;
        if (!value) {
            error = "A one time code is required"
        } else if (!/^[0-9]*$/i.test(value)) {
            error = "Your code only contains digits"
        } else if (value.length !== 6) {
            error = "Your code must only be 6 digits"
        }
        return error
    }
}

export default new validate()
