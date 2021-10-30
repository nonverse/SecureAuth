import React from "react";
import {Formik} from "formik";
import Form from "../Form";
import Field from "../Field";
import validate from "../../Scripts/validate";

const Password = ({load, user, updateUser, advance, back}) => {

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    function submit(values) {
        //
    }

    return (
        <div className="content-wrapper">
            <h4>Secure it with a password</h4>
            <span className="default">First make sure your account data is correct</span>
            <div className="summary">
                <span>Name: <span className="op-05">{`${user.firstname} ${user.lastname}`}</span></span>
                <span>Email: <span className="op-05">{user.email}</span></span>
                <span>Username: <span className="op-05">{user.username}</span></span>
            </div>
            <span className="default">If any of this data is incorrect, please go back and correct it before submitting</span>
            <Formik initialValues={{
                password: '',
                password_confirmation: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form submitCta={"Submit"}>
                        <Field password placeholder={"Password"} validate={validate.require} name={"password"} errors={errors}/>
                        <Field password placeholder={"Confirm Password"} validate={validate.require} name={"password_confirmation"} errors={errors}/>
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