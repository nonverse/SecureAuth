import Loader from "./Loader";
import {useEffect} from "react";

const SwitchUser = () => {

    const query = new URLSearchParams(window.location.search)

    useEffect(() => {
        async function handle() {
            await axios.post('https://auth.nonverse.test/switch-user', query)
                .then(response => {
                    if (response.data.success) {
                        window.location = response.data.data.redirect_uri
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
