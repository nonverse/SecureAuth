import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import LinkButton from "../elements/LinkButton";
import {useNavigate} from "react-router-dom";
import {auth} from "../../../scripts/api/auth";
import {useState} from "react";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";

const Password = ({user, setUser, setInitialized, intended, advance}) => {

    const [error, setError] = useState('')
    const dispatch = useDispatch()
    const navigate = useNavigate()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('login', {
            email: user.email,
            password: values.password
        })
            .then((response) => {
                if (response.data.data.complete) {
                    dispatch(endLoad())
                    setInitialized(false)
                    window.location.replace(`http://${intended.host}${intended.resource}`)
                } else if (response.data.data.authentication_token) {
                    setUser({
                        ...user,
                        authentication_token: response.data.data.authentication_token
                    })
                    dispatch(endLoad())
                    advance()
                }
            })
            .catch((e) => {
                switch (e.response.status) {
                    case 401:
                        setError('Password is incorrect')
                        break;
                    default:
                        setError('Something went wrong')
                }
                dispatch(endLoad())
            })
    }

    function validatePassword(value) {
        setError('')
        return validate.require(value)
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
                        <Field doesLoad password name={"password"} placeholder={"Enter Your Password"} error={errors.password ? errors.password : error}
                               validate={validatePassword}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Password;
