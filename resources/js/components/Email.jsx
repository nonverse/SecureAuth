import Fluid from "./Fluid";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import React, {useState} from "react";
import InLineButton from "../elements/InLineButton";
import validate from "../scripts/validate";
import {auth} from "../scripts/api/auth";
import {useDispatch} from "react-redux";
import {updateUser} from "../state/user";
import {useNavigate} from "react-router-dom";
import {updateLoader} from "../state/loader";
import MessageBox from "./MessageBox";

const Email = () => {

    const dispatch = useDispatch()
    const navigate = useNavigate()
    const [error, setError] = useState('')
    const [message, setMessage] = useState({})

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    return (
        <Fluid heading="Login to continue" subHeading="What's your email?">
            <Formik initialValues={{
                email: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('api/user/initialize', values)
                    .then(response => {
                        dispatch(updateUser({
                            ...response.data.data,
                            email: values.email
                        }))
                        navigate(`/login?${new URLSearchParams(window.location.search)}`)
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 404: {
                                dispatch(updateUser({
                                    email: values.email
                                }))
                                navigate('/register')
                                break
                            }
                            default: {
                                setError('Something went wrong')
                            }
                        }
                    })
            }}>
                {({errors}) => (
                    <>
                        <Form id="fluid-form" cta="Continue">
                            <Field name="email" label="E-Mail" validate={validateEmail}
                                   error={errors.email ? errors.email : error}/>
                            <div className="fluid-actions">
                                <InLineButton id="no-account" onClick={() => {
                                    setMessage({
                                        no_account: true
                                    })
                                }}>Don't have an account?</InLineButton>
                                <a href="https://docs.nonverse.net/legal/privacy-policy" target="_blank"
                                   rel="noreferrer">Privacy Policy</a>
                            </div>
                        </Form>
                        {message.no_account ?
                            <MessageBox id="no-account" onClose={() => {
                                setMessage({
                                    'no_account': false
                                })
                            }}>
                                <p>
                                    Enter your email and continue. If an account is not found, you
                                    will automatically be taken to the registration process
                                </p>
                            </MessageBox>
                            : ''}
                    </>
                )}
            </Formik>
        </Fluid>
    )
}

export default Email
