import React from "react";
import {Formik} from "formik";
import Form from "../Form";
import Field from "../Field";
import validate from "../../Scripts/validate";

const Username = ({load, user, updateUser, advance, back}) => {

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    function submit(values) {
        load(true)
        setTimeout(() => {
            updateUser({
                ...user,
                username: values.username,
            })
            load(false)
            advance()
        }, 1200)
    }

    return (
        <div className="content-wrapper">
            <h4>Choose a username</h4>
            <span>{`${user.firstname} ${user.lastname}`}</span>
            <Formik initialValues={{
                username: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field placeholder={"Username"} validate={validate.require} name={"username"} errors={errors}/>
                        <span className="default">Your username will be your public identifier and is visible to everyone</span>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={previous}>Back</span>
            </div>
        </div>
    )
}

export default Username
