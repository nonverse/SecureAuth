class Auth {

    constructor() {
        this.url = `${import.meta.env.VITE_AUTH_SERVER}/`
    }

    async clearUser() {
        return await auth.post('api/user/cookie', {
            _method: 'delete'
        })
    }
}

export const auth = axios.create({
    baseURL: Auth.url,
    headers: {
        Accept: 'application/json'
    }
})

export default new Auth()
