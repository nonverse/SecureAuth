import LinkButton from "../elements/LinkButton";
import FormInformation from "../elements/FormInformation";
import Button from "../elements/Button";

const OAuthClientInformation = ({response, submit}) => {
    return (
        <>
            <div className="fluid-text">
                <span>{response.client ? response.client.name : null}</span>
                <h1>Would like to access your account</h1>
                <LinkButton action={() => {
                    //
                }}>Use a different account</LinkButton>
            </div>
            <div className="fluid-body">
                <h1><span className="splash">{response.client ? response.client.name : null}</span> will be able to</h1>
                <ul>
                    {(response.client && response.scopes.length !== 0)
                        ? response.scopes.map(scope =>
                            (
                                <li key={scope.replace(/\s+/g, '-').toLowerCase()}>{scope}</li>
                            ))
                        : (
                            <>
                                <li>View all edit of your data</li>
                                <li>Manage your account</li>
                            </>
                        )}
                </ul>
            </div>
            <FormInformation weight={'warning'}>
                Please ensure that you trust <span
                className="splash">{response.client ? response.client.name : null}</span>
                <br/>
                Make sure you read their privacy policy to find out how your data will be handled as you may be sharing
                sensitive information with this application
                <br/>
            </FormInformation>
            <FormInformation weight={'default'}>
                You can always see and manage connections in your <a className="link"
                                                                     href="https://my.nonverse.net/account/apps"
                                                                     rel="noreferrer" target="_blank">Account
                Applications</a> page
            </FormInformation>
            <div className="fluid-actions">
                <LinkButton action={() => submit()}>Deny</LinkButton>
                <Button action={() => submit(true)}>Approve</Button>
            </div>
        </>
    )
}

export default OAuthClientInformation;
