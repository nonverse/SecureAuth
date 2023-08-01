import {combineReducers, configureStore} from "@reduxjs/toolkit";
import userReducer from "../state/user";
import loaderReducer from "../state/loader"
import clientReducer from "../state/client"
import notificationReducer from "./notification";
import usersReducer from "./users";

export default configureStore({
    reducer: {
        user: userReducer,
        users: usersReducer,
        client: clientReducer,
        loader: loaderReducer,
        notification: notificationReducer
    },
})
