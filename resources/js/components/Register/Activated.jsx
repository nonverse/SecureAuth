import React from "react";
import Button from "../elements/Button";

const Activated = ({load, userData, advance}) => {
    return (
        <div className="content-wrapper">
            <h4>Account Activated</h4>
            <span>{userData.email}</span>
            <p>
                Your account has been activated for<br/><span className="default bold">15 MINUTES</span><br/>You must
                complete
                registration within this timeframe to fully activate your account and gain access to the Nonverse
                Network.
                <br/>
            </p>
            <div className="reg-button">
                <div className="button-wrapper" onClick={() => {
                    load(true)
                    setTimeout(() => {
                        load(false)
                        advance()
                    }, 300)
                }}>
                    <Button label="Register"/>
                </div>
            </div>

        </div>
    )
}

export default Activated
