import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useEffect, useState} from "react";
import FormInformation from "../elements/FormInformation";

const PasswordRecovery = ({user, setInitialized}) => {

    const [error, setError] = useState('')
    const [sent, setSent] = useState(false)

    useEffect(() => {
        setInitialized(true)
    })

    async function submit(values) {

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

export default PasswordRecovery;
