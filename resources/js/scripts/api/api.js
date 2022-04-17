import axios from "axios";

class api {

    constructor() {

        // API Location
        this.url = `${(process.env.MIX_DEV === 'true') ? 'http' : 'https'}://${process.env.MIX_API_SERVER}/`
    }

    async initialiseCsrf() {
        this.initialised = false
        return await axios.get(
            `${this.url}sanctum/csrf-cookie`
        );
    }
}

export default new api()
