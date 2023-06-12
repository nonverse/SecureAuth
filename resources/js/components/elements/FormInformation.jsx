import {motion} from "framer-motion";
import LinkButton from "./LinkButton";

const FormInformation = ({weight, id, close, children}) => {

    return (
        <motion.div className="form-info-wrapper"
                    id={id}
                    initial={{opacity: 0}}
                    animate={{opacity: 1}}
                    exit={{opacity: 0}}
                    transition={{duration: .5}}
        >
            <div className={`form-info form-info-${weight}`}>
                {children}
            </div>
            {close ? <LinkButton action={close}>Close</LinkButton> : ''}
        </motion.div>
    )
}

export default FormInformation;
