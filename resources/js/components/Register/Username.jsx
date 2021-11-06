import React, {useState} from "react";
import {Formik} from "formik";
import Form from "../elements/Form";
import Field from "../elements/Field";
import validate from "../../scripts/validate";

const Username = ({load, user, updateUser, advance, back}) => {

    const [error, setError] = useState('');

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    async function submit(values) {
        load(true)
        await validate.validateNewUser(values.username)
            .then((response) => {
                updateUser({
                    ...user,
                    username: values.username
                })
                advance()
            }).catch((e) => {
                setError('That username is already taken')
            })
        load(false);
    }

    function validateUsername(value) {
        setError('')
        return validate.require(value)
    }

    return (
        <div className="content-wrapper">
            <h4>Choose a username</h4>
            <span>{`${user.name_first} ${user.name_last}`}</span>
            <Formik initialValues={{
                username: user.username ? user.username : '',
            }} onSubmit={(values) => {
                submit(values)
            }}>
                {({values, errors}) => (
                    <Form>
                        <Field placeholder={"Username"} validate={validateUsername} name={"username"}
                               error={errors.username ? errors.username : error}
                               value={values.username}/>
                        <span className="default">Your username will be your public identifier and is visible to everyone</span>
                    </Form>
                )}
            </Formik>
            <div className="links">
                <span className="link-btn" onClick={previous}>Back</span>
            </div>
        </div>
    )
}

export default Username
