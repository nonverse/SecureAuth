import {combineReducers, configureStore} from "@reduxjs/toolkit";
import userReducer from "../state/user";
import loaderReducer from "../state/loader"

export default configureStore({
    reducer: {
        user: userReducer,
        loader: loaderReducer
    },
})
