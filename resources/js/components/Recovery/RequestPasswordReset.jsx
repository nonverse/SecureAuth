import Fluid from "../Fluid";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import {auth} from "../../scripts/api/auth";
import {useState} from "react";
import {useDispatch} from "react-redux";
import {updateLoader} from "../../state/loader";

const RequestPasswordReset = ({setState}) => {

    const [error, setError] = useState('')
    const dispatch = useDispatch()

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    return (
        <Fluid id="password-recovery-request" heading="Account Recovery" subHeading="Reset password">
            <Formik initialValues={{
                email: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('/recovery/password', values)
                    .then(response => {
                        if (response.data.success) {
                            setState(1)
                            dispatch(updateLoader(false))
                        }
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 404:
                                setError('Unable to find account with this email')
                                break
                            case 400:
                                setError(e.response.data.errors.email)
                                break
                            default:
                                setError('Something went wrong')
                        }
                        dispatch(updateLoader(false))
                    })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Submit">
                        <Field name="email" label="E-Mail" validate={validateEmail} error={errors.email ? errors.email : error}/>
                        <div className="fluid-text">
                            <p>
                                Enter the email associated with your account to receive instructions to reset your
                                password
                            </p>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default RequestPasswordReset
