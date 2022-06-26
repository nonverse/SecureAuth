import {useEffect, useState} from "react";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../../state/load";
import {auth} from "../../../../scripts/api/auth";
import validate from "../../../../scripts/validate";
import LinkButton from "../../elements/LinkButton";
import FormInformation from "../../elements/FormInformation";
import {Formik} from "formik";
import Form from "../../elements/Form";
import Field from "../../elements/Field";

const RequestPasswordReset = () => {

    const [error, setError] = useState('')
    const [sent, setSent] = useState(false)
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('recovery/password', {
            email: values.email
        })
            .then((response) => {
                if (response.data.data.success) {
                    dispatch(endLoad())
                    setSent(true)
                }
            })
            .catch((e) => {
                switch (e.response.status) {
                    case 400:
                        setError(e.response.data.errors.email)
                        break;
                    default:
                        setError('Something went wrong')
                }
                dispatch(endLoad())
            })
    }

    function validateEmail(value) {
        setError('')
        return validate.email(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Account Recovery</span>
                <h1>Reset your password</h1>
                <LinkButton action={() => {
                    window.location.replace('/login')
                }}>Restart Login</LinkButton>
            </div>
            {sent ? (
                <div>
                    <FormInformation weight={'success'}>
                        An email has been sent to you containing instructions to reset your password.
                    </FormInformation>
                </div>
            ) : (
                <Formik initialValues={{}} onSubmit={(values) => {
                    submit(values)
                }}>
                    {({errors}) => (
                        <div>
                            <Form cta={"Continue"}>
                                <Field doesLoad name={"email"} placeholder={"What's your email?"}
                                       error={errors.email ? errors.email : error} validate={validateEmail}/>
                            </Form>
                        </div>
                    )}
                </Formik>
            )}
        </>
    )
}

export default RequestPasswordReset;
