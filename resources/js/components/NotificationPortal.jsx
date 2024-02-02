import {useSelector} from "react-redux";
import {AnimatePresence} from "framer-motion";
import Notification from "@/components/Notification.jsx";

const NotificationPortal = () => {

    const notification = useSelector(state => state.notification.value)

    return (
        <div className="notification-portal">
            <AnimatePresence mode="wait">
                {notification ? (
                    <>
                        {notification.message ? (
                            <Notification>
                                {notification.message}
                            </Notification>
                        ) : ''}
                    </>
                ): ''}
            </AnimatePresence>
        </div>
    )
}

export default NotificationPortal
