import InLineButton from "../elements/InLineButton";
import {motion} from "framer-motion";

const MessageBox = ({id, weight, onClose, children}) => {

    return (
        <motion.div className="message-box-container"
                    id={id}
                    key={id}
                    initial={{opacity: 0}}
                    animate={{opacity: 1}}
                    exit={{opacity: 0}}
                    transition={{duration: .1}}>

            <div className={`message-box message-box-${weight}`}>
                {children}
            </div>
            {onClose ? <InLineButton onClick={() => {
                onClose()
            }}>Close</InLineButton> : ''}
        </motion.div>
    )
}

export default MessageBox
