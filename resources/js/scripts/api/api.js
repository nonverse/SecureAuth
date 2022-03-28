import axios from "axios";

class api {

    constructor() {

        // API Location
        this.url = `https://api.nonverse.net/`
    }

    async initialiseCsrf() {
        this.initialised = false
        return await axios.get(
            `${this.url}sanctum/csrf-cookie`
        );
    }
}

export default new api()
