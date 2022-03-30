import axios from "axios";

class api {

    constructor() {

        // API Location
        this.url = `http${process.env.MIX_DEV ? '' : 's'}://${process.env.MIX_API_SERVER}/`
    }

    async initialiseCsrf() {
        this.initialised = false
        return await axios.get(
            `${this.url}sanctum/csrf-cookie`
        );
    }
}

export default new api()
