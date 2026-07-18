<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email Address</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #faf5f6; padding: 20px;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-top: 4px solid #d5001c; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <h2 style="color: #d5001c; margin-top: 0;">GovAssist</h2>
        <p style="color: #333333; font-size: 16px; line-height: 1.5;">Hello,</p>
        <p style="color: #333333; font-size: 16px; line-height: 1.5;">
            Thank you for registering. Please use the following One-Time Password (OTP) to verify your email address. 
            This code will expire in 15 minutes.
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #d5001c; background-color: #f9f9f9; padding: 15px 25px; border: 1px solid #eeeeee; border-radius: 4px;">
                {{ $otpCode }}
            </span>
        </div>
        
        <p style="color: #777777; font-size: 14px; line-height: 1.5;">
            If you did not create an account, no further action is required.
        </p>
        <p style="color: #777777; font-size: 14px; line-height: 1.5; margin-bottom: 0;">
            Regards,<br>SSFO San Miguel
        </p>
    </div>
</body>
</html>
