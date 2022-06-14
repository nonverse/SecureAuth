import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";
import validate from "../../scripts/validate";
import {useState} from "react";

const Email = () => {

    const [user, setUser] = useState({})

    async function submit(values) {
        setUser({
            email: values.email
        })
    }

    return (
        <Formik initialValues={{
            email: ''
        }} onSubmit={(values) => {
            submit(values)
        }}>
            {({errors}) => (
                <Form cta={"Continue"}>
                    <Field name={"email"} placeholder={"What's your email?"} error={errors.email} validate={validate.email}/>
                </Form>
            )}
        </Formik>
    )
}

export default Email;
