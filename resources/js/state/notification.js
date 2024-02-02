import {createSlice} from "@reduxjs/toolkit";

export const notificationSlice = createSlice({
    name: 'notification',
    initialState: {},
    reducers: {
        sendNotification: (state, action) => {
            state.value = action.payload
        },

        closeNotification: (state) => {
            state.value = {}
        },
    }
})

export const {sendNotification, closeNotification} = notificationSlice.actions
export default notificationSlice.reducer
