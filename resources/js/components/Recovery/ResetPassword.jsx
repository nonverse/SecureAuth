import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";
import auth from "../../scripts/api/auth";

const ResetPassword = ({load, advance}) => {

    const [error, setError] = useState('')

    async function submit(values) {
        load(true)
        await auth.reset(values)
            .then((response) => {
                let data = response.data
                if (data.data.success) {
                    advance()
                } else {
                    setError(data.errors.password)
                }
            }).catch((e) => {
                setError(e.response.data.errors.password)
            })
        load(false)
    }

    function validateConfirm(value, compare) {
        setError('')
        return validate.confirmation(value, compare)
    }

    return (
        <div className="content-wrapper">
            <h4>Account Recovery</h4>
            <span>Reset Password</span>
            <Formik initialValues={{
                password: '',
                password_confirmation: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors, values}) => (
                    <Form submitCta={"Submit"}>
                        <Field password placeholder={"Password"} validate={validate.require} name={"password"}
                               error={errors.password ? errors.password : error}/>
                        <Field password placeholder={"Confirm Password"} validate={value =>
                            validateConfirm(value, values.password)
                        }
                               name={"password_confirmation"}
                               error={errors.password_confirmation}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={() => window.location.replace('/login')}>Back to login</span>
            </div>
        </div>
    )
}

export default ResetPassword
