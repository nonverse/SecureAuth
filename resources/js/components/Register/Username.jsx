import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";

const Username = (user, setUser) => {

    async function submit(values) {
        setUser({
            ...user,
            username: values.username
        })
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello</span>
                <h1>{`${user.first_name} ${user.last_name}`}</h1>
                <LinkButton>Back</LinkButton>
            </div>
            <Formik initialValues={{
                username: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form cta={"Continue"}>
                        <Field name={"username"} placeholder={"Create a username"} error={errors.username}
                               validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Username;
