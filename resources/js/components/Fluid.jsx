import {motion} from "framer-motion";

const Fluid = ({heading, subHeading, children}) => {

    return (
        <motion.div className="fluid"
                    key={`${heading}-${subHeading}`}
                    initial={{opacity: 0, x: 150}}
                    animate={{opacity: 1, x: 0}}
                    exit={{opacity: 0, x: -150}}
                    transition={{duration: .1}}
        >
            <div className="fluid-heading">
                <h1>{heading}</h1>
                <h2>{subHeading}</h2>
            </div>
            <div className="fluid-content">
                {children}
            </div>
        </motion.div>
    )
}

export default Fluid
