import React, {useState} from "react";
import {useHistory} from "react-router-dom";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";

const TwoFactorCheckpoint = ({load, userData}) => {

    const history = useHistory()
    const [error, setError] = useState('')

    async function submit(values) {
        //
    }

    function validateOtp(value) {
        setError('');
        return validate.otp(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Two Factor Authentication</h4>
            <span>Your account is protected by 2FA</span>
            <Formik initialValues={{
                otp: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form submitCta={"Verify"}>
                        <Field placeholder={"Code"} validate={validateOtp} name={"otp"} error={errors.otp ? errors.otp : error}/>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn">Can't access authenticator?</span>
                <span className="link-btn" onClick={() => window.location.reload()}>Restart login</span>
            </div>
        </div>
    )
}

export default TwoFactorCheckpoint
