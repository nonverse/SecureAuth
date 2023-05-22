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

const Email = () => {

    const dispatch = useDispatch()
    const navigate = useNavigate()
    const [error, setError] = useState('')

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
                        navigate('/login')
                    })
                    .catch(e => {
                        switch (e.response.status) {
                            case 404: {
                                break
                            }
                            default: {
                                setError('Something went wrong')
                            }
                        }
                    })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" cta="Continue">
                        <Field name="email" label="E-Mail" validate={validateEmail}
                               error={errors.email ? errors.email : error}/>
                        <div className="fluid-actions">
                            <InLineButton id="no-account">Don't have an account?</InLineButton>
                            <InLineButton id="email-privacy">Privacy Policy</InLineButton>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Email
