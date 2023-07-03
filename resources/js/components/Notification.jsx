import {motion} from "framer-motion";
import {useDispatch, useSelector} from "react-redux";
import InLineButton from "@/elements/InLineButton.jsx";
import {closeNotification} from "@/state/app/notification.js";
import {useEffect} from "react";

const Notification = () => {

    const notification = useSelector(state => state.notification.value)
    const dispatch = useDispatch()

    useEffect(() => {
        setTimeout(() => {
            dispatch(closeNotification())
        }, notification.timeout ? notification.timeout : 5000)
    }, [])

    return (
        <motion.div className={`notification notification-${notification.weight}`}
                    key={`notification-${notification.message}`}
                    initial={{width: 0, x: "100%", opacity: 0}}
                    animate={{width: "100%", x: 0, opacity: 1}}
                    exit={{width: 0, x: 0, opacity: 0}} // TODO Fix exit animation
                    transition={{duration: .1}}>
            <span className="notification-message">{notification.message}</span>
            <InLineButton onClick={() => {
                dispatch(closeNotification())
            }}>Close</InLineButton>
        </motion.div>
    )
}

export default Notification
