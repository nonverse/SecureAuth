import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";
import {auth} from "../../scripts/api/auth";
import {updateLoader} from "../../state/loader";

const Authorization = () => {

    const client = useSelector(state => state.client.value)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()

    return (
        <>{client ? (
            <>
                {(client.name === 'unauthorized') ? (
                    <p id="client-error">
                        You are not authorized to access this application
                    </p>
                ) : (
                    <Fluid id="oauth-authorization" heading={client.name}
                           subHeading="Wants to access your Nonverse account">
                        <div className="fluid-text">
                            <p>
                                {client.name} is requesting access to your Nonverse account.
                                <br/><br/>
                                This application will be able to
                                <br/>
                            </p>
                            <ul id="oauth-scopes">
                                {client.scope.map(scope => (
                                    <li key={scope.id}>{scope.description}</li>
                                ))}
                            </ul>
                            <p>
                                <br/>
                                By clicking 'Approve', you
                                allow {client.name} to use your information as listed above in
                                accordance with their respective terms of service and privacy policy(s)
                                <br/>
                                You can remove this application at any time in your <a target="_blank" rel="noreferrer"
                                                                                       href="https://account.nonverse.net/security">account
                                page</a>
                            </p>
                        </div>
                        <div id="authorize-actions">
                            <InLineButton id="oauth-deny" onClick={async () => {
                                dispatch(updateLoader(true))
                                await auth.post('/oauth/authorize/deny', query)
                                    .then(() => {
                                        window.location = `${query.get('redirect_uri')}?code=authorization_denied`
                                    })
                            }}>Deny</InLineButton>
                            <InLineButton id="oauth-approve" onClick={async () => {
                                dispatch(updateLoader(true))
                                await auth.post('/oauth/authorize', query)
                                    .then(response => {
                                        if (response.data.data.approved) {
                                            window.location = `${query.get('redirect_uri')}?code=${response.data.data.code}`
                                        }
                                    })
                            }}>Approve</InLineButton>
                        </div>
                    </Fluid>
                )}
            </>
        ) : (
            <p id="client-error">
                Client validation failed
                <br/>
                If you are the developer of this client, please see the network logger for errors
            </p>
        )
        }</>
    )
}

export default Authorization
