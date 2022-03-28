import axios from "axios";

class api {

    constructor() {

        // API Location
        this.url = `http://${process.env.REACT_APP_API_SERVER}/`
    }

    async initialiseCsrf() {
        this.initialised = false
        return await axios.get(
            `${this.url}sanctum/csrf-cookie`
        );
    }
}

export default new api()
