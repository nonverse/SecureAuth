import Fluid from "../Fluid";
import {useDispatch, useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";
import {updateUser} from "../../state/user";

const Agreement = ({advance}) => {

    const dispatch = useDispatch()
    const user = useSelector(state => state.user.value)

    function progress() {
        dispatch(updateUser({
            ...user,
            eula: true,
            privacy_policy: true
        }))

        advance()
    }

    return (
        <Fluid id="register-agreement" heading="Welcome" subHeading={user.email}>
            <div className="fluid-text">
                <p>
                    It looks like you are new to Nonverse
                    <br/><br/>
                    You will be required to create an account before you can access any
                    Nonverse applications or services
                    <br/>
                    By continuing, you agree to the Nonverse <a href="https://docs.nonverse.net/legal/eula"
                                                                target="_blank" rel="noreferrer">EULA</a>{' and '}
                    <a href="https://docs.nonverse.net/legal/privacy-policy"
                       target="_blank" rel="noreferrer">privacy policy</a>
                </p>
            </div>
            <InLineButton id="agree-eula" onClick={() => {
                progress()
            }}>Continue</InLineButton>
        </Fluid>
    )
}

export default Agreement
