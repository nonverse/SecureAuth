import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import auth from "../../scripts/api/auth";
import user from "../../scripts/api/user";
import validate from "../../scripts/validate";

const Password = ({load, userData, updateUser, advance, back}) => {

    const [error, setError] = useState('');

    async function previous() {
        load(true)
        await user.deleteCookie()
        back()
        load(false)
    }

    async function submit(values) {
        load(true)
        await auth.login({
            ...userData,
            ...values,
            keep_authenticated: false,
        }).then((response) => {
            let data = response.data.data
            if (data.complete) {
                return window.location.replace(`https://${data.host}${data.resource}`)
            } else {
                updateUser({
                    ...userData,
                    auth_token: data.auth_token
                })
                advance();
            }
        }).catch(() => {
            setError('Password is incorrect')
        })
        load(false)
    }

    function validatePassword(values) {
        setError('')
        return validate.require(values)
    }

    return (
        <div className="content-wrapper">
            <span>Welcome back</span>
            <h4>{`${userData.name_first} ${userData.name_last}`}</h4>
            <span className="link-btn" onClick={previous}>Not You?</span>
            <Formik initialValues={{
                password: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form submitCta={"Login"}>
                        <Field password placeholder={"Password"} validate={validatePassword} name={"password"}
                               error={errors.password ? errors.password : error}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={() => {
                    window.location.replace('/forgot')
                }}>Forgot Password?</span>
            </div>
        </div>
    )
}

export default Password
