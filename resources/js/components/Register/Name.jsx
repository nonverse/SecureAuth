import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Field from "../elements/Field";
import Form from "../elements/Form";
import validate from "../../../scripts/validate";

const Name = ({user, setUser}) => {

    async function submit(values) {
        setUser({
            ...user,
            first_name: values.first_name,
            last_name: values.last_name
        })
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello</span>
                <h1>Looks like you're new here!</h1>
                <LinkButton>Use a different email</LinkButton>
            </div>
            <Formik initialValues={{
                first_name: '',
                last_name: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form cta={"Continue"}>
                        <Field name={"name_first"} placeholder={"What's your first name"} error={errors.first_name} validate={validate.require}/>
                        <Field name={"name_lasts"} placeholder={"...and your surname"} error={errors.last_name} validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Name;
