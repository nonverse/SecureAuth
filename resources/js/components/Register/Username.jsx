import LinkButton from "../elements/LinkButton";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../../scripts/validate";
import {useDispatch} from "react-redux";
import {endLoad, startLoad} from "../../state/load";
import {auth} from "../../../scripts/api/auth";
import {useState} from "react";
import {end} from "@popperjs/core";

const Username = ({user, setUser, advance}) => {

    const [error, setError] = useState('')
    const dispatch = useDispatch()

    async function submit(values) {

        dispatch(startLoad())

        await auth.post('api/validator/username', {
            username: values.username
        })
            .then(() => {
                setUser({
                    ...user,
                    username: values.username
                })
                dispatch(endLoad())
                advance()
            })
            .catch((e) => {
                switch (e.response.status) {
                    case 422:
                        setError('This username is taken')
                        break;
                    default:
                        setError('Something went wrong')
                }
                dispatch(endLoad())
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
                        <Field doesLoad name={"username"} placeholder={"Create a username"} error={errors.username}
                               validate={validate.require}/>
                    </Form>
                )}
            </Formik>
        </>
    )
}

export default Username;
