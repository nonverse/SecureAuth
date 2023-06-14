import Fluid from "../Fluid";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";
import validate from "../../scripts/validate";
import {auth} from "../../scripts/api/auth";
import {useState} from "react";
import {useDispatch} from "react-redux";
import {updateLoader} from "../../state/loader";

const Password = () => {

    const query = new URLSearchParams(window.location.search)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    function validatePassword(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <Fluid heading="Authorization Required" subHeading="Update E-Mail">
            <Formik initialValues={{
                password: ''
            }} onSubmit={async (values) => {
                dispatch(updateLoader(true))
                await auth.post('authorize', {
                    ...values,
                    action_id: query.get('action_id')
                })
                    .then(response => {
                        if (response.data.data.authorized) {
                            window.location = `https://${query.get('host')}${query.get('resource')}?authorization_token=${response.data.data.authorization_token}`
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
                        <div className="fluid-text">
                            <p>
                                For your security, we need to verify that it's really you who is making this change to
                                your account
                            </p>
                        </div>
                    </Form>
                )}
            </Formik>
        </Fluid>
    )
}

export default Password
