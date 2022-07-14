
class dictionary {

    constructor() {
        this.actions = {
            account_purge: "Delete Account",
            api_key_purge: "Delete API Key",
            password_update: "Update Password"
        }
    }

    actionByKey(key) {
        return this.actions[key]
    }
}

export default new dictionary()
