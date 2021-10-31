import React from "react";
import {ClipLoader} from "react-spinners";

const Loader = (complete) => {

    return (
        <div className="loader-container">
            <div className="loader">
                <ClipLoader color={'#3f9ae5'} size={50}/>
            </div>
        </div>
    )
}

export default Loader
