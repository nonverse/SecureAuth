import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import user from "../../scripts/api/user";
import validate from "../../scripts/validate";

const VerifyEmail = ({load, userData, updateUser, advance}) => {

    const [error, setError] = useState('')

    async function submit(values) {
        load(true)
        await user.activate({
            email: userData.email,
            ...values
        })
            .then((response) => {
                if (response.data.data.success) {
                    updateUser({
                            ...userData,
                            activation_token: response.data.data.activation_token
                        }
                    )
                    advance()
                }
            })
            .catch((e) => {
                if (e.response.status === 401) {
                    setError(e.response.data.errors.activation_key)
                } else {
                    setError('Something went wrong')
                }
            })
        load(false)
    }

    function validateKey(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Activate your account</h4>
            <span>{userData.email}</span>
            <Formik initialValues={{
                activation_key: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field placeholder="Activation Key" validate={validateKey}
                               error={errors.activation_key ? errors.activation_key : error} name="activation_key"/>
                        <span className="default">
                            Please enter the activation key that was included in your email invitation.
                        </span>
                    </Form>
                )}
            </Formik>

            <div className="links">
                {/*<span className="link-btn" onClick={() => window.location.replace('/login')}>Login</span>*/}
                <a className="link-btn" href="http://nonverse.net/request-invitation" target="_blank" rel="noreferrer">Don't
                    have an invite?</a>
            </div>
        </div>
    )
}

export default VerifyEmail
