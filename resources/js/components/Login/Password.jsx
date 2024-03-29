import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import {Formik} from "formik";
import Form from "../../elements/Form";
import {useEffect, useState} from "react";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import InLineButton from "../../elements/InLineButton";
import {auth} from "../../scripts/api/auth";
import {updateLoader} from "../../state/loader";
import {updateUser} from "../../state/user";

const Password = ({advance}) => {

    const user = useSelector(state => state.user.value)
    const users = useSelector(state => state.users.value)
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
                await axios.post(`https://auth.nonverse.test/login`, {
                    ...values,
                    email: user.email
                }, {
                    withCredentials: true
                })
                    .then(response => {
                        if (response.data.complete) {
                            return window.location = `https://${query.get('host') ? query.get('host') : 'account.nonverse.test'}${query.get('resource') ? query.get('resource') : '/'}`
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
                            <InLineButton id="forgot-password" onClick={() => {
                                window.location.replace('/recovery/password')
                            }}>Forgot Password</InLineButton>
                            <InLineButton id="not-you" onClick={() => {
                                window.history.replaceState(null, document.title, window.location.pathname)
                                {
                                    users ? advance(2) : window.location = '/'
                                }
                            }}>Not you?</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
