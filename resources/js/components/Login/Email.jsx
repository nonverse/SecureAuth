import React from "react";
import {Formik} from "formik";
import validate from "../../Scripts/validate";

import Form from "../Form";
import Field from "../Field";

const Email = ({load, updateUser, advance}) => {

    return (
        <div className="content-wrapper">
            <h4>Login to continue</h4>
            <span>Nonverse Studios</span>
            <Formik initialValues={{
                email: '',
            }} onSubmit={(values) => {
                load(true)
                console.log(values);
                updateUser({
                    email: values.email,
                })
                setTimeout(() => {
                    load(false)
                    advance()
                }, 1200)
            }}>
                {({errors}) => (
                    <Form>
                        <Field placeholder={"Email"} validate={validate.email} name={"email"} errors={errors}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <a href="#">Forgot your email?</a>
                <a href="#">Create account</a>
            </div>
        </div>
    )
}

export default Email
