import React from "react";

const PasswordResetEmailSent = () => (
    <div className="content-wrapper">
        <h4>Account Recovery</h4>
        <span>Reset Password</span>
        <p className="default">
            Your password has successfully been reset and you can now use it to login to your account. Please note that resetting your password DOES NOT disable Two Factor Authentication.
            If you have lost access to  your authenticator app, please contact support
        </p>
        <div className="links">
            <span className="link-btn" onClick={() => window.location.replace('/login')}>Back to login</span>
            <span className="link-btn">Contact support</span>
        </div>
    </div>
)

export default PasswordResetEmailSent
