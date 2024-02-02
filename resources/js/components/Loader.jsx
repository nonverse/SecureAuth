import {ClipLoader} from "react-spinners";

const Loader = (complete) => {

    return (
        <div className="loader-container">
            <div className="loader">
                <ClipLoader color={'#6951FF'} size={50}/>
            </div>
        </div>
    )
}

export default Loader
