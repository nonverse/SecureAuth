import axios from "axios";

class api {

    constructor() {

        // Variables
        this.initialised = false

        // API Location
        this.url = 'http://api.nonverse.test/'
    }

    async initialiseCsrf() {
        this.initialised = false
        await axios.get(
            `${this.url}sanctum/csrf-cookie`
        ).then(() => {
            this.initialised = true;
        })
    }
}

export default new api()
