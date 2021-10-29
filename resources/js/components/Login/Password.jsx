import React from "react";
import {Formik} from "formik";
import validate from "../../Scripts/validate";

import Form from "../Form";
import Field from "../Field";

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
                {({errors}) => (
                    <Form submitCta={"Login"}>
                        <Field password placeholder={"Password"} validate={validate.require} name={"password"}
                               errors={errors}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <a href="#">Forgot Password?</a>
            </div>
        </div>
    )
}

export default Password
