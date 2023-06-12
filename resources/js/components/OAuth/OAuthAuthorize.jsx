import {useEffect, useState} from "react";
import FormInformation from "../elements/FormInformation";
import {auth} from "../../../scripts/api/auth";
import OAuthClientInformation from "./OAuthClientInformation";
import {useDispatch} from "react-redux";
import {startLoad} from "../../state/load";

const OAuthAuthorize = ({user, setInitialized}) => {

    const [response, setResponse] = useState({})
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    useEffect(async () => {
        await auth.post('api/oauth/authorize', null, {params: query})
            .then((response) => {
                if (response.data.data.redirect_uri) {
                    window.location.replace(response.data.data.redirect_uri)
                } else {
                    setResponse(response.data.data)
                    setInitialized(true)
                }
            })
            .catch((e) => {
                setResponse(e.response.data)
                setInitialized(true)
            })

    }, [])

    async function submit(approve) {
        const params = Object.fromEntries(query)

        setInitialized(false)

        await auth.post('oauth/authorize', {
            state: params.state,
            client_id: params.client_id,
            auth_token: response.auth_token,
            _method: approve ? null : 'delete'
        })
            .then((response) => {
                window.location.replace(response.data.data.redirect_uri)
            })
    }

    return (
        <>
            {response.error ? (
                <FormInformation weight={'danger'}>{response.error_description}</FormInformation>
            ) : (
                <OAuthClientInformation response={response} submit={submit}/>
            )}
        </>
    )
}

export default OAuthAuthorize
