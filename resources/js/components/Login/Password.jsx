import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Form from "../../elements/Form";
import {useEffect, useState} from "react";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import InLineButton from "../../elements/InLineButton";
import Auth, {auth} from "../../scripts/api/auth";
import {updateLoader} from "../../state/loader";
import {updateUser} from "../../state/user";

const Password = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const query = new URLSearchParams(window.location.search)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    useEffect(() => {
        dispatch(updateLoader(false))
    }, [])

    function validatePassword(value) {
        setError('')
        return validate.require(value)
    }

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
                            const url = `${query.get('host') ? 'https://' + query.get('host') : process.env.MIX_ACCOUNT_APP}${query.get('resource') ? query.get('resource') : '/'}`
                            return window.location.replace(url)
                        } else {
                            dispatch(updateUser({
                                ...user,
                                authentication_token: response.data.data.authentication_token
                            }))
                            advance()
                        }
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 401:
                                setError('Password is incorrect')
                                break
                            default:
                                setError('Something went wrong')
                        }
                        dispatch(updateLoader(false))
                    })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <Field password name="password" label="Password" validate={validatePassword}
                               error={errors.password ? errors.password : error}/>
                        <div className="fluid-actions">
                            <InLineButton id="forgot-password">Forgot Password</InLineButton>
                            <InLineButton id="not-you" onClick={async () => {
                                await Auth.clearUser()
                                    .then(() => {
                                        window.location.replace('/')
                                    })
                            }}>Not you?</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
