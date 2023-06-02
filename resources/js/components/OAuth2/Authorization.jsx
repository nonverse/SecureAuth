import Fluid from "../Fluid";
import {useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";

const Authorization = () => {

    const client = useSelector(state => state.client.value)

    return (
        <>{client ? (
            <Fluid id="oauth-authorization" heading={client.name} subHeading="Wants to access your Nonverse account">
                <div className="fluid-text">
                    <p>
                        Nonverse Account is requesting access to your Nonverse account.
                        <br/><br/>
                        This application will be able to
                        <br/>
                    </p>
                    <ul id="oauth-scopes">
                        {client.scopes.map(scope => (
                            <li key={scope.id}>{scope.description}</li>
                        ))}
                    </ul>
                    <p>
                        <br/>
                        By clicking 'Approve', you
                        allow Nonverse Account to use your information as listed above in
                        accordance with their respective terms of service and privacy policy(s)
                        <br/>
                        You can remove this application at any time in your <a target="_blank" rel="noreferrer"
                                                                               href="https://account.nonverse.net/security">account
                        page</a>
                    </p>
                </div>
                <div id="authorize-actions">
                    <InLineButton id="oauth-deny">Deny</InLineButton>
                    <InLineButton id="oauth-approve">Approve</InLineButton>
                </div>
            </Fluid>
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
