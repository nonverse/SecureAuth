import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import LinkButton from "../elements/LinkButton";
import {useNavigate} from "react-router-dom";
import {auth} from "../../../scripts/api/auth";

const Password = ({user, setUser, advance}) => {

    const navigate = useNavigate()

    async function submit(values) {
        setUser({
            ...user,
            password: values.password
        })
    }

    return (
        <>
            <div className="fluid-text">
                <span>Welcome back</span>
                <h1>{`${user.name_first} ${user.name_last}`}</h1>
                <LinkButton action={async () => {
                    await auth.post('api/user/cookie', {
                        _method: 'delete'
                    })
                        .then(() => {
                            navigate('/')
                        })
                }}>Not You?</LinkButton>
            </div>
            <Formik initialValues={{
                password: ''
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({errors}) => (
                    <Form>
                        <Field password name={"password"} placeholder={"Enter Your Password"} error={errors.password}
                               validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Password;
