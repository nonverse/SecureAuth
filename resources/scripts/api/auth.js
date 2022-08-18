import axios from "axios";

let url = `http://auth.nonverse.test`

class auth_base {

    constructor() {
        this.url = url
    }

    async initialise() {
        //
    }
}

export const auth = axios.create({
    baseURL: url,
    headers: {
        Accept: 'application/json'
    }
})

export default new auth_base();
