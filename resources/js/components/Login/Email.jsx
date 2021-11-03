import React, {useState} from "react";
import {Formik} from "formik";
import validate from "../../scripts/validate";
import Form from "../elements/Form";
import Field from "../elements/Field";
import {useHistory} from "react-router-dom";
import auth from "../../scripts/api/auth";

const Email = ({load, updateUser, advance}) => {

    const history = useHistory()
    const [error, setError] = useState('')

    async function submit(values) {
        load(true)
        await auth.verifyEmail(values.email)
            .then((response) => {
                let user = response.data.data
                updateUser({
                    email: user.email,
                    name_first: user.name_first,
                    name_last: user.name_last
                })
                advance();
            }).catch((e) => {
                setError('Unable to find an account with that email')
            })
        load(false)
    }

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Login to continue</h4>
            <span>Nonverse Studios</span>
            <Formik initialValues={{
                email: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field placeholder={"Email"} validate={validateEmail} name={"email"} error={errors.email ? errors.email : error}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn">Forgot your email?</span>
                <span className="link-btn" onClick={() => history.push('/register')}>Create Account</span>
            </div>
        </div>
    )
}

export default Email
