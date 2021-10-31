import React from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";
import user from "../../scripts/api/user";

const Password = ({load, userData, back}) => {

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    async function submit(values) {
        load(true)
        await user.create({
            ...userData,
            ...values
        })
        load(false)

        // TODO Error handling if post fails
    }

    return (
        <div className="content-wrapper">
            <h4>Secure it with a password</h4>
            <span className="default">First make sure your account data is correct</span>
            <div className="summary">
                <span>Name: <span className="op-05">{`${userData.name_first} ${userData.name_last}`}</span></span>
                <span>Email: <span className="op-05">{userData.email}</span></span>
                <span>Username: <span className="op-05">{userData.username}</span></span>
            </div>
            <span
                className="default">If any of this data is incorrect, please go back and correct it before submitting</span>
            <Formik initialValues={{
                password: '',
                password_confirmation: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form submitCta={"Submit"}>
                        <Field password placeholder={"Password"} validate={validate.require} name={"password"}
                               errors={errors}/>
                        <Field password placeholder={"Confirm Password"} validate={validate.require}
                               name={"password_confirmation"} errors={errors}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={previous}>Back</span>
            </div>
        </div>
    )
}

export default Password
