import React from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";

const Name = ({load, user, updateUser, advance, back}) => {

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
                firstname: values.firstname,
                lastname: values.lastname,
            })
            load(false)
            advance()
        }, 1200)
    }

    return (
        <div className="content-wrapper">
            <h4>What's your name?</h4>
            <span>{user.email}</span>
            <Formik initialValues={{
                firstname: user.firstname ? user.firstname : '',
                lastname: user.lastname ? user.lastname : '',

            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({values, errors}) => (
                    <Form>
                        <Field placeholder={"First Name"} validate={validate.require} name={"firstname"}
                               errors={errors} value={values.firstname}/>
                        <Field placeholder={"Last Name"} validate={validate.require} name={"lastname"} errors={errors}
                               value={values.lastname}/>
                        <span className="default">Your name not be shown to others without your consent</span>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={previous}>Back</span>
            </div>
        </div>
    )
}

export default Name
