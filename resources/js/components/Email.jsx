import {Formik} from "formik";
import Form from "./elements/Form";
import Field from "./elements/Field";
import validate from "../../scripts/validate";
import {useNavigate} from "react-router-dom";
import {auth} from "../../scripts/api/auth";
import {useEffect, useState} from "react";
import {useDispatch, useSelector} from "react-redux";
import {endLoad, startLoad} from "../state/load";
import LinkButton from "./elements/LinkButton";
import FormInformation from "./elements/FormInformation";

const Email = ({setUser, setInitialized}) => {

    const [showInfo, setShowInfo] = useState(false)
    const load = useSelector((state) => state.loader.value)
    const query = new URLSearchParams(window.location.search)
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
                let host = encodeURIComponent(query.get('host') ? query.get('host') : 'my.nonverse.net')
                let resource = encodeURIComponent(query.get('resource') ? query.get('resource') : '/')

                dispatch(endLoad())
                navigate(`login?host=${host}&resource=${resource}`)
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
        <>
            <div className="fluid-text">
                <span>Nonverse Studios</span>
                <h1>Login to continue</h1>
            </div>
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
                        <LinkButton action={() => {
                            setShowInfo(true)
                        }}>Don't have an account?</LinkButton>
                    </div>
                )}
            </Formik>
            {showInfo ? (
                <FormInformation weight={'warning'} close={() => {
                    setShowInfo(false)
                }}>
                You will be asked to create an account before logging in
            </FormInformation>
            ) : ''}
        </>
    )
}

export default Email;
