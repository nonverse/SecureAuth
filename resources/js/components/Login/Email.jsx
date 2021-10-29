import React, {useState} from "react";
import {Formik, Field, Form} from "formik";
import Button from "../../elements/Button";

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
                <Form>
                    <Field id={"email"} name={"email"} placeholder={"Email"}/>
                    <div className="button-wrapper">
                        <Button label={"Continue"} submit/>
                    </div>
                </Form>
            </Formik>
            <div className="links">
                <a href="#">Forgot your email?</a>
                <a href="#">Create account</a>
            </div>
        </div>
    )
}

export default Email
