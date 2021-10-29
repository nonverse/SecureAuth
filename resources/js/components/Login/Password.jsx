import React, {useState} from "react";
import Form from "../../elements/Form";
import Field from "../../elements/Field";

const Password = ({load, user, advance, back}) => {

    const [password, setPassword] = useState('')

    function submit() {
        load(true)
        console.log("Password Submit")

        setTimeout(() => {
            load(false)
        }, 1200)
    }

    function previous() {
        load(true)
        setTimeout(() => {
            load(false);
            back()
        }, 500)
    }

    return (
        <div className="content-wrapper">
            <span>Welcome back</span>
            <Form title={user.email} submit={submit}>
                <span className="link-btn" onClick={previous}>Not You?</span>
                <Field password update={setPassword} value={password} name={"Password"}/>
            </Form>
        </div>
    )
}

export default Password
