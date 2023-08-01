import Fluid from "../Fluid";
import Card from "../../elements/Card";
import {useDispatch, useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";
import {useNavigate} from "react-router-dom";
import {updateUser} from "../../state/user";

const AccountSelector = ({restart}) => {

    const users = useSelector(state => state.users.value)
    const dispatch = useDispatch()
    const navigate = useNavigate()

    return (
        <Fluid heading="Welcome back" subHeading="Choose an account">
            {Object.keys(users).map((user) => (
                <Card key={user} name={`${users[user]['name_first']} ${users[user]['name_last']}`}
                      value={users[user]['email']} onClick={() => {
                    dispatch(updateUser({
                        email: users[user]['email'],
                        name_first: users[user]['name_first'],
                        name_last: users[user]['name_last']
                    }))
                    restart()
                }}/>
            ))}
            <InLineButton id="add-account" onClick={() => {
                navigate('/')
            }}>Add another account</InLineButton>
        </Fluid>
    )
}

export default AccountSelector
