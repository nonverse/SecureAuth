import LinkButton from "../elements/LinkButton";
import {useNavigate} from "react-router-dom";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useState} from "react";
import {auth} from "../../../scripts/api/auth";

const Activate = ({user, setUser, advance}) => {

    const navigate = useNavigate()
    const [error, setError] = useState('')

    async function submit(values) {

        await auth.post('api/validator/activation-key', {
            email: user.email,
            activation_key: values.activation_key
        })
            .then((response) => {
                if (response.data.data.success) {
                    setUser({
                        ...user,
                        activation_key: values.activation_key
                    })
                    advance()
                }
            })
            .catch((e) => {
                setError(e.response.data.errors.activation_key)
            })
    }

    function validateKey(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello</span>
                <h1>Looks like you're new here!</h1>
                <LinkButton action={() => {
                    navigate('/')
                }}>Use a different email</LinkButton>
            </div>
            <Formik initialValues={{
                activation_key: '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form cta={"Continue"}>
                        <Field name={"activation_key"} placeholder={"Enter your activation key"}
                               error={errors.activation_key ? errors.activation_key : error} validate={validateKey}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Activate;
