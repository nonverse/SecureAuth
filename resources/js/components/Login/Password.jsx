import React, {useState} from "react";
import {Formik, Field, Form} from "formik";
import Button from "../../elements/Button";

const Password = ({load, user, advance, back}) => {

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    return (
        <div className="content-wrapper">
            <span>Welcome back</span>
            <h4>{user.email}</h4>
            <span className="link-btn" onClick={previous}>Not You?</span>
            <Formik initialValues={{
                password: '',
            }} onSubmit={(values) => {
                load(true)

                setTimeout(() => {
                    load(false)
                }, 500)
            }}>
                <Form>
                    <Field type={"password"} id={"password"} name={"password"} placeholder={"Password"}/>
                    <div className="button-wrapper">
                        <Button label={"Login"} submit/>
                    </div>
                </Form>
            </Formik>
            <div className="links">
                <a href="#">Forgot Password?</a>
            </div>
        </div>
    )
}

export default Password
