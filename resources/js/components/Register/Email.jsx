import React from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";
import {useHistory} from "react-router-dom";

const Email = ({load, user, updateUser, advance}) => {

    const history = useHistory()

    function submit(values) {
        load(true)

        setTimeout(() => {
            updateUser({
                email: values.email,
            })
            load(false)
            advance()
        }, 1200)
    }

    return (
        <div className="content-wrapper">
            <h4>Create an account</h4>
            <span>Nonverse Studios</span>
            <Formik initialValues={{
                email: user.email ? user.email : '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({values, errors}) => (
                    <Form>
                        <Field placeholder={"Email"} validate={validate.email} name={"email"} errors={errors}
                               value={values.email}/>
                        <span className={"default"}> By continuing you consent to your email being collected and sent to Nonverse servers for verification</span>
                    </Form>
                )}
            </Formik>

            <div className="links">
                <span className="link-btn" onClick={() => history.push('/login')}>Login</span>
            </div>
        </div>
    )
}

export default Email
