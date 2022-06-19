import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";

const Username = ({user, setUser, advance}) => {

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
                <h1>{`${user.name_first} ${user.name_last}`}</h1>
                <LinkButton action={() => {
                    advance(1)
                }}>Back</LinkButton>
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
