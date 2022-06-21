import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";
import validate from "../../scripts/validate";
import {useNavigate} from "react-router-dom";
import {auth} from "../../scripts/api/auth";
import {useEffect} from "react";

const Email = ({setUser, setInitialized}) => {

    const navigate = useNavigate()

    useEffect(() => {
        setInitialized(true)
    })

    async function submit(values) {

        await auth.post('api/user/initialize', {
            email: values.email
        })
            .then(response => {
                setUser({
                    uuid: response.data.data.uuid,
                    email: values.email,
                    name_first: response.data.data.name_first,
                    name_last: response.data.data.name_last
                })
                navigate('login')
            })
            .catch(() => {
                setUser({
                    email: values.email
                })
                navigate('register')
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
