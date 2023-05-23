class Auth {

    constructor() {
        this.url = `${process.env.MIX_AUTH_SERVER}/`
    }
}

export const auth = axios.create({
    baseURL: Auth.url,
    headers: {
        Accept: 'application/json'
    }
})

export default new Auth()
