import axios from "axios";

let url = `http://auth.nonverse.test`
let token = ''

class auth_base {

    constructor() {
        this.url = url
    }

    async initialise() {
        await axios.get(
            `${url}sanctum/csrf-cookie`
        )
            .then(() => {
                token = document.cookie.split("; ")
                    .find(cookie => cookie.startsWith("XSRF-TOKEN="))
                    .split("=")[1]
            })
        //console.log(`2: ${token}`)
    }
}

export const auth = axios.create({
    baseURL: url,
    headers: {
        Accept: 'application/json',
        'X-XSRF-TOKEN': token
    }
})

export default new auth_base();
