import {useEffect, useState} from "react";
import validate from "../../scripts/validate";
import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";
import LinkButton from "./elements/LinkButton";
import FormInformation from "./elements/FormInformation";

const ConfirmPassword = ({user, setInitialized}) => {

    const [error, setError] = useState('')
    const [showInfo, setShowInfo] = useState(false)

    useEffect(() => {
        setInitialized(true)
    })

    async function submit(values) {
        //
    }

    function validatePassword(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello, <span className="op-05">{user.name_first} {user.name_last}</span></span>
                <h1>Confirm your password</h1>
                <LinkButton action={() => {

                }}>Back to app</LinkButton>
            </div>
            <Formik initialValues={{
                password: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <div>
                        <Form>
                            <Field doesLoad password name={"password"} placeholder={"Enter your password"}
                                   error={errors.password ? errors.password : error} validate={validatePassword}/>
                        </Form>
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>Why do I need to confirm my password?</LinkButton>
                    </div>
                )}
            </Formik>
            {showInfo ?
                (
                    <FormInformation weight={'warning'} close={() => {
                        setShowInfo(false)
                    }}>
                        Some actions require you to confirm your password so we can verify that it's really you who is
                        executing them.
                    </FormInformation>
                ) : ''}
        </>
    )
}

export default ConfirmPassword;
