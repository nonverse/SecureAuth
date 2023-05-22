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

const Password = () => {

    const user = useSelector(state => state.user.value)
    const [loading, setLoading] = useState(false)
    const dispatch = useDispatch()

    useEffect(() => {
        dispatch(updateLoader(false))
    })

    return (
        <Fluid heading={`Welcome back, ${user.name_first}`} subHeading="What's your password?">
            <Formik initialValues={{
                password: ''
            }} onSubmit={async (values) => {
                setLoading(true)
                await auth.post('login', {
                    ...values,
                    email: user.email
                })
            }}>
                {({errors}) => (
                    <Form id="fluid-form" loading={loading} cta="Continue">
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
