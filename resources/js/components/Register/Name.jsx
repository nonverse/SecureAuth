import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Field from "../elements/Field";
import Form from "../elements/Form";
import validate from "../../../scripts/validate";
import {useNavigate} from "react-router-dom";

const Name = ({user, setUser, advance}) => {

    const navigate = useNavigate()

    async function submit(values) {
        setUser({
            ...user,
            name_first: values.name_first,
            name_last: values.name_last
        })
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
                name_first: '',
                name_last: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form cta={"Continue"}>
                        <Field name={"name_first"} placeholder={"What's your first name"} error={errors.first_name} validate={validate.require}/>
                        <Field name={"name_last"} placeholder={"...and your surname"} error={errors.last_name} validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Name;
