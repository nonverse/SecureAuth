import React, {useEffect, useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";

const ForgotPassword = ({load, setInitialised, userData, updateUser}) => {

    const [error, setError] = useState('')

    async function submit() {
        //
    }

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    useEffect(() => {
        setInitialised(true)
    })

    return (
        <div className="content-wrapper">
            <h4>Account Recovery</h4>
            <span>Forgot Password</span>
            <Formik initialValues={{
                email: '',
            }} onSubmit={() => {
                submit()
            }}>
                {({errors}) => (
                    <Form submitCta={"Submit"}>
                        <Field placeholder={"Email"} validate={validateEmail} name={"email"} error={errors.email ? errors.email : error}/>
                        <span className="default">An email will be sent to your email with instructions to reset your password</span>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={() => window.location.replace('/login')}>Back to login</span>
            </div>
        </div>
    )
}

export default ForgotPassword
