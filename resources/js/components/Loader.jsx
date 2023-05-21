import {ClipLoader} from "react-spinners";

const Loader = ({message}) => {

    return (
        <div className="loader-wrapper">
            <ClipLoader size={"3rem"} color={"#6951FF"}/>
        </div>
    )
}

export default Loader 