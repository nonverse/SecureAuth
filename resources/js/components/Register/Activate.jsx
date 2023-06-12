import LinkButton from "../elements/LinkButton";
import {useNavigate} from "react-router-dom";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useState} from "react";
import {auth} from "../../../scripts/api/auth";
import {useDispatch} from "react-redux";
import {startLoad, endLoad} from "../../state/load";
import FormInformation from "../elements/FormInformation";

const Activate = ({user, setUser, advance}) => {

    const navigate = useNavigate()
    const [showInfo, setShowInfo] = useState(false)
    const [error, setError] = useState('')
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('api/validator/activation-key', {
            email: user.email,
            activation_key: values.activation_key
        })
            .then((response) => {
                if (response.data.data.success) {
                    setUser({
                        ...user,
                        activation_key: values.activation_key
                    })
                    dispatch(endLoad())
                    advance()
                }
            })
            .catch((e) => {
                setError(e.response.data.errors.activation_key)
                dispatch(endLoad())
            })
    }

    function validateKey(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <>
            <div className="fluid-text">
                <span>Hello <span className="op-05">{user.email}</span></span>
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
                    <div>
                        <Form cta={"Continue"}>
                            <Field doesLoad name={"activation_key"} placeholder={"Enter your activation key"}
                                   error={errors.activation_key ? errors.activation_key : error} validate={validateKey}/>
                        </Form>
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>I don't have an activation key</LinkButton>
                    </div>
                )}
            </Formik>
            {showInfo ? (
                <FormInformation weight={"warning"} close={() => {
                    setShowInfo(false)
                }}>
                    Your account activation key was included in your email invitation. If you did not receive an invitation, you can request one <a
                    href="https://www.nonverse.net" target="_blank" rel="noreferrer">here</a>
                </FormInformation>
            ) : ''}
        </>
    )
}

export default Activate;
