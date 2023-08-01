import Fluid from "../Fluid";
import Card from "../../elements/Card";
import {useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";
import {useNavigate} from "react-router-dom";

const AccountSelector = () => {

    const users = useSelector(state => state.users.value)
    const navigate = useNavigate()

    return (
        <Fluid heading="Welcome back" subHeading="Choose an account">
            {Object.keys(users).map((user) => (
                <Card key={user} name={`${users[user]['name_first']} ${users[user]['name_last']}`} value={users[user]['email']}/>
            ))}
            <InLineButton id="add-account" onClick={() => {
                navigate('/')
            }}>Add another account</InLineButton>
        </Fluid>
    )
}

export default AccountSelector
