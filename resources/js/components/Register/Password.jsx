import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import InLineButton from "../../elements/InLineButton";
import {auth} from "../../scripts/api/auth";
import {updateLoader} from "../../state/loader";

const Password = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const dispatch = useDispatch()

    return (
        <Fluid id="register-password" heading={`Almost there, ${user.name_first}`} subHeading="Create a password">
            <Formik initialValues={{
                password: '',
                password_confirmation: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))

                await auth.post('register', {
                    ...user,
                    ...values
                })
                    .then(response => {
                        if (response.data.data.uuid) {
                            window.location = 'https://account.nonverse.test'
                        }
                    })
                    .catch(e => {
                        //TODO Show error
                    })
            }}>
                {({values, errors}) => (
                    <Form id="fluid-form" cta="Finish">
                        <Field password name="password" label="Password"
                               validate={value => validate.password(value, [user.name_first, user.name_last, user.email, user.username])}
                               error={errors.password}/>
                        <Field password name="password_confirmation" label="Confirm Password"
                               validate={value => validate.confirmation(value, values.password)}
                               error={errors.password_confirmation}/>
                        <div className="fluid-text">
                            <p>
                                Choose a strong password to secure your account.
                                <br/>
                                Your password MUST NOT contain any pieces of information that may be used to identify
                                you, such as your
                                name, username or e-mail.
                                <br/><br/>
                                Your password MUST be at least 8 characters long and contain a mix of alphanumeric and
                                special
                                characters
                            </p>
                        </div>
                        <div className="fluid-actions">
                            <InLineButton onClick={() => {
                                advance(true)
                            }}>Back</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
