import {Outlet} from "react-router-dom";
import {motion} from "framer-motion";
import {useSelector} from "react-redux";

const Fluid = () => {

    const load = useSelector((state) => state.loader.value)

    return (
        <motion.div className="fluid-container"
                    initial={{opacity: 0}}
                    animate={{opacity: 1}}
                    exit={{opacity: 0}}
                    transition={{duration: .5}}
        >
            <div className="fluid">
                <div className={load ? 'form-loading action-cover op-05' : ''}>
                    <Outlet/>
                </div>
            </div>
        </motion.div>
    )
}

export default Fluid;
