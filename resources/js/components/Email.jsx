import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";
import validate from "../../scripts/validate";
import {useNavigate} from "react-router-dom";
import {auth} from "../../scripts/api/auth";
import {useEffect} from "react";
import {useDispatch, useSelector} from "react-redux";
import {endLoad, startLoad} from "../state/load";

const Email = ({setUser, setInitialized}) => {

    const load = useSelector((state) => state.loader.value)
    const dispatch = useDispatch()
    const navigate = useNavigate()

    useEffect(() => {
        setInitialized(true)
    })

    async function submit(values) {

        dispatch(startLoad())

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
                dispatch(endLoad())
                navigate('login')
            })
            .catch(() => {
                setUser({
                    email: values.email
                })
                dispatch(endLoad())
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
                <div className={load ? 'form-loading action-cover op-05' : ''}>
                    <Form cta={"Continue"}>
                        <Field doesLoad name={"email"} placeholder={"What's your email?"} error={errors.email} validate={validate.email}/>
                    </Form>
                </div>
            )}
        </Formik>
    )
}

export default Email;
