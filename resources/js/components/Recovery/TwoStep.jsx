import Fluid from "../Fluid";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import {auth} from "../../scripts/api/auth";
import {useDispatch} from "react-redux";
import {updateLoader} from "../../state/loader";
import {useState} from "react";

const TwoStep = () => {

    const query = new URLSearchParams(window.location.search)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    function validateToken(value) {
        setError('')
        return validate.require(value, 24, 24)
    }

    return (
        <Fluid heading="Account Recovery" subHeading="Reset two-step">
            <Formik initialValues={{
                totp_recovery_token: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))

                await auth.post('/login/two-factor', {
                    ...values,
                    authentication_token: query.get('token')
                })
                    .then(response => {
                        if (response.data.data.complete) {
                            window.location = 'https://account.nonverse.test'
                        }
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 401:
                                setError('Invalid recovery token')
                                break
                            default:
                                setError('Something went wrong')
                        }
                        dispatch(updateLoader(false))
                    })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Submit">
                        <Field name="totp_recovery_token" label="Recovery Token"
                               validate={validateToken}
                               error={errors.totp_recovery_token ? 'Please enter a valid recovery token' : error}/>
                        <div className="fluid-text">
                            <p>
                                Please note that recovering your account using a recovery token will disable Two-Step
                                login on your account.
                            </p>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default TwoStep
