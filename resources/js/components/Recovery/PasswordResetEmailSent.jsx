import React from "react";

const PasswordResetEmailSent = ({userData}) => (
    <div className="content-wrapper">
        <h4>Account Recovery</h4>
        <span>Forgot Password</span>
        <p className="default">
            An email has been sent to {userData.email} containing instructions to reset your password.
            If you do not have access to this email, please contact support
        </p>
        <div className="links">
            <span className="link-btn" onClick={() => window.location.replace('/login')}>Back to login</span>
            <span className="link-btn">Contact support</span>
        </div>
    </div>
)

export default PasswordResetEmailSent
