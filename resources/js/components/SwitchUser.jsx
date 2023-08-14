import Loader from "./Loader";
import {useEffect} from "react";

const SwitchUser = () => {

    const query = new URLSearchParams(window.location.search)

    useEffect(() => {
        async function handle() {
            await axios.post('https://auth.nonverse.test/switch-user', query)
                .then(response => {
                    if (response.data.complete) {
                        if (response.data.data.redirect_uri) {
                            return window.location = response.data.data.redirect_uri
                        }
                        return window.location = `https://${query.get('host') ? query.get('host') : 'account.nonverse.test'}${query.get('resource') ? query.get('resource') : '/'}`
                    }
                })
                .catch(e => {
                    //
                })
        }

        handle()
    }, [])

    return (
        <Loader/>
    )
}

export default SwitchUser
