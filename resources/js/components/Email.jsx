import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";

const Email = () => {

    async function submit() {

    }

    return (
        <Formik initialValues={{
            email: ''
        }} onSubmit={() => {
            submit()
        }}>
            {({errors}) => (
                <Form cta={"Continue"}>
                    <Field name={"email"} placeholder={"What's your email?"} error={errors.email}/>
                </Form>
            )}
        </Formik>
    )
}

export default Email;
