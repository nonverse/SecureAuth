import Fluid from "../Fluid";
import Card from "../../elements/Card";
import {useDispatch, useSelector} from "react-redux";
import InLineButton from "../../elements/InLineButton";
import {useNavigate} from "react-router-dom";
import {updateUser} from "../../state/user";

const AccountSelector = ({restart}) => {

    const users = useSelector(state => state.users.value)
    const query = new URLSearchParams(window.location.search)
    const dispatch = useDispatch()
    const navigate = useNavigate()

    return (
        <Fluid heading="Welcome back" subHeading="Choose an account">
            {Object.keys(users).map((user) => (
                <Card key={user} name={`${users[user]['data']['name_first']} ${users[user]['data']['name_last']}`}
                      value={users[user]['data']['email']}
                      info={users[user]['session']['is_valid'] ? 'Logged In' : ''}
                      onClick={async () => {
                          if (users[user]['session']['is_valid']) {
                              await axios.post(`https://auth.nonverse.test/login`, {
                                  uuid: user
                              }, {
                                  withCredentials: true
                              })
                                  .then(response => {
                                      if (response.data.complete) {
                                          return window.location = `https://${query.get('host') ? query.get('host') : 'account.nonverse.test'}${query.get('resource') ? query.get('resource') : '/'}`
                                      }
                                      dispatch(updateUser(response.data.data))
                                      restart()
                                  })
                          }
                          dispatch(updateUser({
                              email: users[user]['data']['email'],
                              name_first: users[user]['data']['name_first'],
                              name_last: users[user]['data']['name_last']
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
