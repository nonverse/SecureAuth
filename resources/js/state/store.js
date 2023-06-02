import {combineReducers, configureStore} from "@reduxjs/toolkit";
import userReducer from "../state/user";
import loaderReducer from "../state/loader"
import clientReducer from "../state/client"

export default configureStore({
    reducer: {
        user: userReducer,
        client: clientReducer,
        loader: loaderReducer
    },
})
