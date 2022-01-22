import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";
import auth from "../../scripts/api/auth";

const TwoFactorRecovery = ({load, user}) => {

    const [error, setError] = useState('')

    async function submit(values) {
        load(true)
        await auth.twofactor(user.auth_token, null, values.recovery_token)
            .then((response) => {
                let data = response.data.data
                if (data.complete) {
                    return window.location.replace(`http://${data.host}${data.resource}`)
                }
            }).catch((e) => {
                let status = e.response.status
                if (status === 400) {
                    setError('Session has expired, please restart login')
                } else if (status === 401) {
                    setError('Incorrect Token')
                }
            })
        load(false);
    }

    function validateToken(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Two Factor Authentication</h4>
            <span>Your account is protected by 2FA</span>
            <Formik initialValues={{
                recovery_token: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form submitCta="Verify">
                        <Field placeholder="Recovery Token" name="recovery_token" validate={validateToken}
                               error={errors.recovery_token ? errors.recovery_token : error}/>
                        <span className="default">Your recovery token was emailed to you when you last enabled 2FA</span>
                        <span className="danger default">This will disable 2FA on your account</span>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn">Contact support</span>
                <span className="link-btn" onClick={() => window.location.reload()}>Restart login</span>
            </div>
        </div>
    )
}

export default TwoFactorRecovery
