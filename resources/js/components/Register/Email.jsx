import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";

const Email = ({load, updateUser, advance}) => {

    const [error, setError] = useState('')
    const query = new URLSearchParams(window.location.search);

    async function submit(values) {
        load(true)
        await validate.validateNewEmail(values.email)
            .then((response) => {
                updateUser({
                    email: values.email
                })
                advance()
            }).catch((e) => {
                setError('This email is already registered')
            })
        load(false)
    }

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Create an account</h4>
            <span>Nonverse Studios</span>
            <Formik initialValues={{
                email: query.get('email'),
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({values, errors}) => (
                    <Form>
                        <Field placeholder={"Email"} validate={validateEmail} name={"email"}
                               error={errors.email ? errors.email : error}
                               value={values.email}/>
                        <span className={"default"}> By continuing you consent to your email being collected and sent to Nonverse servers for verification</span>
                    </Form>
                )}
            </Formik>

            <div className="links">
                <span className="link-btn" onClick={() => window.location.replace('/login')}>Login</span>
            </div>
        </div>
    )
}

export default Email
