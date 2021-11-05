import React, {useState} from "react";
import {Formik} from "formik";
import validate from "../../scripts/validate";
import Form from "../elements/Form";
import Field from "../elements/Field";
import auth from "../../scripts/api/auth";

const Password = ({load, user, updateUser, advance, back}) => {

    const [error, setError] = useState('');

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    async function submit(values) {
        load(true)
        await auth.login({
            ...user,
            ...values,
            keep_authenticated: false,
        }).then((response) => {
            let data = response.data.data
            if (data.complete) {
                window.location.replace(`https://${data.host}${data.resource}`)
            } else {
                updateUser({
                    ...user,
                    uuid: data.uuid,
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
            <h4>{`${user.name_first} ${user.name_last}`}</h4>
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
                <a href="#">Forgot Password?</a>
            </div>
        </div>
    )
}

export default Password
