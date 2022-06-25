import {motion} from "framer-motion";

const FormInformation = ({weight, id, children}) => {

    return (
        <motion.div className={`form-info form-info-${weight}`}
                    id={id}
                    initial={{opacity: 0}}
                    animate={{opacity: 1}}
                    exit={{opacity: 0}}
                    transition={{duration: .5}}
        >
            {children}
        </motion.div>
    )
}

export default FormInformation;
