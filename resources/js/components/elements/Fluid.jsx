import {Outlet} from "react-router-dom";
import {motion} from "framer-motion";

const Fluid = () => {

    return (
        <motion.div className="fluid-container"
                    initial={{opacity: 0}}
                    animate={{opacity: 1}}
                    exit={{opacity: 0}}
                    transition={{duration: .5}}
        >
            <div className="fluid">
                <Outlet/>
            </div>
        </motion.div>
    )
}

export default Fluid;
