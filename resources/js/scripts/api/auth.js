class Auth {

    constructor() {
        this.url = `http://${process.env.REACT_APP_AUTH_SERVER}/`
    }
}

export const auth = axios.create({
    baseURL: Auth.url,
    headers: {
        Accept: 'application/json'
    }
})

export default new Auth()
