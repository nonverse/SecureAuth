import Fluid from "../Fluid";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import {useDispatch} from "react-redux";
import {updateLoader} from "../../state/loader";
import {auth} from "../../scripts/api/auth";
import {useNavigate} from "react-router-dom";

const ResetPassword = () => {

    const dispatch = useDispatch()
    const query = new URLSearchParams(window.location.search)
    const navigate = useNavigate()

    return (
        <Fluid id="password-recovery-reset" heading="Account Recovery" subHeading="Reset password">
            <Formik initialValues={{
                password: '',
                password_confirmation: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('/recovery/password/reset', {
                    ...values,
                    token: query.get('token'),
                    email: query.get('email')
                })
                    .then(response => {
                        if (response.data.success) {
                            navigate('/')
                        }
                        dispatch(updateLoader(false))
                    })
            }}>
                {({values, errors}) => (
                    <Form id="fluid-form" cta="Submit">
                        <Field password name="password" label="New Password"
                               validate={value => validate.password(value, ['blacklist'])} error={errors.password}/>
                        <Field password name="password_confirmation" label="Confirm New Password"
                               validate={value => validate.confirmation(value, values.password)}
                               error={errors.password_confirmation}/>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default ResetPassword
