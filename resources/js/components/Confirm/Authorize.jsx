import {useEffect, useState} from "react";
import ProgressiveForm from "../elements/ProgressiveForm";
import {useSelector} from "react-redux";
import dictionary from "../../../scripts/dictionary";
import AuthorizePassword from "./AuthorizePassword";
import AuthorizeTwoFactor from "./AuthorizeTwoFactor";

const Authorize = ({user, setUser, setInitialized}) => {

    const [state, setState] = useState(1)
    const [invalid, setInvalid] = useState(false)
    const load = useSelector((state) => state.loader.value)
    const query = new URLSearchParams(window.location.search)
    const baseUrl = `https://${decodeURIComponent(query.get('host'))}${decodeURIComponent(query.get('resource'))}`

    useEffect(() => {
        if (!dictionary.actionByKey(query.get('action_id'))) {
            setInvalid(true)
        }
        setInitialized(true)
    })

    function advance(target) {
        if (!target) {
            setState(state + 1)
        } else {
            setState(target)
        }
    }

    function createRedirectUrl(token, token_expiry, token_authenticates) {
        return `${baseUrl}${baseUrl.includes('?') ? '&' : '?'}authorization_token=${token}&token_expiry=${token_expiry}&token_authenticates=${token_authenticates}`
    }

    return (
        <div className={load ? 'form-loading action-cover op-05' : ''}>
            <ProgressiveForm
                state={state}
                forms={{
                    1: <AuthorizePassword user={user} setUser={setUser} baseUrl={baseUrl} redirectUrl={createRedirectUrl} invalid={invalid} setInitialized={setInitialized} advance={advance}/>,
                    2: <AuthorizeTwoFactor user={user} setUser={setUser} baseUrl={baseUrl} redirectUrl={createRedirectUrl} invalid={invalid} setInitialized={setInitialized} advance={advance}/>
                }}
            />
        </div>
    )
}

export default Authorize;
