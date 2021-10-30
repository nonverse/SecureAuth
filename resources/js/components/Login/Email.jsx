import React from "react";
import {Formik} from "formik";
import validate from "../../scripts/validate";

import Form from "../Form";
import Field from "../Field";
import {useHistory} from "react-router-dom";

const Email = ({load, updateUser, advance}) => {

    const history = useHistory()

    function submit(values) {
        load(true)
        console.log(values);
        updateUser({
            email: values.email,
        })
        setTimeout(() => {
            load(false)
            advance()
        }, 1200)
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
                        <Field placeholder={"Email"} validate={validate.email} name={"email"} errors={errors}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <a href="#">Forgot your email?</a>
                <span className="link-btn" onClick={() => history.push('/register')}>Create Account</span>
            </div>
        </div>
    )
}

export default Email
