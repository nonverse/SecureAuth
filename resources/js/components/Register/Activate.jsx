import LinkButton from "../elements/LinkButton";
import {useNavigate} from "react-router-dom";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";

const Activate = ({user, setUser, advance}) => {

    const navigate = useNavigate()

    async function submit(values) {
        setUser({
            ...user,
            activation_key: values.activation_key
        })
        advance()
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
                        <Field name={"activation_key"} placeholder={"Enter your activation key"} error={errors.activation_key} validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Activate;
