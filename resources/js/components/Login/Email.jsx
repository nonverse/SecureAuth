import React, {useState} from "react";
import Form from "../../elements/Form";
import Field from "../../elements/Field";

const Email = ({load, advance}) => {

    const [email, setEmail] = useState('')

    function submit() {
        load(true)
        console.log("Test Component Post Success")

        setTimeout(() => {
            load(false)
            advance()
        }, 1200)


    }

    return (
        <div className="content-wrapper">
            <Form title={"Login To Continue"} submit={submit}>
                <Field update={setEmail} name={"Email"} value={email}/>
            </Form>
            <div className="links">
                <a href="#">Forgot Your Email?</a>
                <a href="#">Create Account</a>
            </div>
        </div>
    )
}

export default Email
