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
        }).then((response) => {
            let data = response.data.data
            if (data.complete) {
                return window.location.replace('https://nonverse.net')
            }
        }).catch((e) => {
            // TODO Error handling if post fails
        })
        load(false)
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
                {({errors, values}) => (
                    <Form submitCta={"Submit"}>
                        <Field password placeholder={"Password"} validate={validate.require} name={"password"}
                               error={errors.password}/>
                        <Field password placeholder={"Confirm Password"}
                               validate={value => validate.confirmation(value, values.password)}
                               name={"password_confirmation"} error={errors.password_confirmation}/>
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
