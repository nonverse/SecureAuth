import {useEffect, useState} from "react";
import LinkButton from "../elements/LinkButton";
import Button from "../elements/Button";
import FormInformation from "../elements/FormInformation";
import {auth} from "../../../scripts/api/auth";

const OAuthAuthorize = ({user, setInitialized}) => {

    const [request, setRequest] = useState({
        client_name: 'Pinterest',
        scopes: [
            'View your email address',
            'View your basic information',
        ]})

    useEffect(() => {
        setInitialized(true)
    }, [])

    async function handle(approve) {
        await auth.post('oauth/authorize', {

        })
    }

    return (
        <>
            <div className="fluid-text">
                <span>{request.client_name}</span>
                <h1>Would like to access your account</h1>
                <LinkButton action={() => {
                    //
                }}>Use a different account</LinkButton>
            </div>
            <div className="fluid-body">
                <h1><span className="splash">{request.client_name}</span> will be able to</h1>
                <ul>
                    {request.scopes.map(scope => (
                        <li key={scope.replace(/\s+/g, '-').toLowerCase()}>{scope}</li>
                    ))}
                </ul>
            </div>
            <FormInformation weight={'warning'}>
                Please ensure that you trust <span className="splash">{request.client_name}</span>
                <br/>
                Make sure you read their privacy policy to find out how your data will be handled as you may be sharing sensitive information with this application
                <br/>
            </FormInformation>
            <FormInformation weight={'default'}>
                You can always see and manage connections in your <a className="link" href="https://my.nonverse.net/account/apps" rel="noreferrer" target="_blank">Account Applications</a> page
            </FormInformation>
            <div className="fluid-actions">
                <LinkButton>Deny</LinkButton>
                <Button>Approve</Button>
            </div>
        </>
    )
}

export default OAuthAuthorize
