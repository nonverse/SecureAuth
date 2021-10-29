import React, {useState} from "react";
import {Formik, Field} from "formik";
import Button from "../../elements/Button";
import validate from "../../Scripts/validate";

import Form from "../Form";

const Email = ({load, updateUser, advance}) => {

    return (
        <div className="content-wrapper">
            <h4>Login to continue</h4>
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
                        <Field className={errors.email ? 'field-error' : ''} id={"email"} name={"email"}
                               placeholder={"Email"} validate={validate.email}/>
                        <span className="error">{errors.email}</span>
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
