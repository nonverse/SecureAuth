import React, {useState} from "react";
import {useHistory} from "react-router-dom";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";

const VerifyEmail = ({load, userData, advance}) => {

    const [error, setError] = useState('')

    async function submit(values) {

    }

    return (
        <div className="content-wrapper">
            <h4>Activate your account</h4>
            <span>{userData.email}</span>
            <Formik initialValues={{
                email_otp: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field placeholder="Activation Key" error={errors.email_otp} name="email_otp"/>
                        <span className="default">
                            Please enter the activation key that was included in your email invitation.
                        </span>
                    </Form>
                )}
            </Formik>

            <div className="links">
                {/*<span className="link-btn" onClick={() => window.location.replace('/login')}>Login</span>*/}
                <a className="link-btn" href="http://nonverse.net/request-invitation" target="_blank" rel="noreferrer">Don't have an invite?</a>
            </div>
        </div>
    )
}

export default VerifyEmail
