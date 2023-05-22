import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Form from "../../elements/Form";
import {useEffect} from "react";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import InLineButton from "../../elements/InLineButton";
import {auth} from "../../scripts/api/auth";
import {updateLoader} from "../../state/loader";
import {updateUser} from "../../state/user";

const Password = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const dispatch = useDispatch()

    useEffect(() => {
        dispatch(updateLoader(false))
    }, [])

    return (
        <Fluid heading={`Welcome back, ${user.name_first}`} subHeading="What's your password?">
            <Formik initialValues={{
                password: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('login', {
                    ...values,
                    email: user.email
                })
                    .then(response => {
                        if (response.data.data.complete) {
                            return window.location.replace(process.env.REACT_APP_ACCOUNT_APP)
                        } else {
                            dispatch(updateUser({
                                ...user,
                                authentication_token: response.data.data.authentication_token
                            }))
                            advance()
                        }
                    })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <Field password name="password" label="Password" validate={validate.require}
                               error={errors.password}/>
                        <div className="fluid-actions">
                            <InLineButton id="forgot-password">Forgot Password</InLineButton>
                            <InLineButton id="not-you">Not you?</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
