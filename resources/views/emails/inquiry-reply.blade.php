<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Response to your Inquiry</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f9fc; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border: 1px solid #e8e8e8; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <tr>
            <td align="center" bgcolor="#b91c1c" style="padding: 30px 0 20px 0; color: #ffffff; font-size: 24px; font-weight: bold; letter-spacing: 1px;">
                GovAssist Helpdesk
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px; line-height: 1.6; color: #333333;">
                <h2 style="color: #1e293b; margin-top: 0;">Response to your Inquiry</h2>
                <p>Hello <strong>{{ $inquiry->user ? $inquiry->user->name : $inquiry->guest_name }}</strong>,</p>
                <p>An administrator has responded to your manual inquiry regarding <strong>{{ $inquiry->service ? $inquiry->service->name_en : 'General Inquiry' }}</strong>.</p>
                
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; margin: 20px 0; font-size: 13px; color: #334155;">
                    <p style="margin-top: 0; font-weight: bold; color: #64748b; font-size: 11px; uppercase;">Your Question:</p>
                    <p style="font-style: italic; margin-bottom: 15px;">"{{ $inquiry->inquiry_text }}"</p>
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 15px;">
                    <p style="margin-top: 0; font-weight: bold; color: #b91c1c; font-size: 11px; uppercase;">Admin Response:</p>
                    <p style="margin-bottom: 0; white-space: pre-line; line-height: 1.6;">{{ $replyMessage }}</p>
                </div>

                <p>If you have any further questions, you can contact the admin desk or check the dashboard.</p>
                <p>Thank you for using GovAssist!</p>
            </td>
        </tr>
        <tr>
            <td align="center" bgcolor="#f1f5f9" style="padding: 20px 0; color: #64748b; font-size: 12px; border-top: 1px solid #e2e8f0;">
                © {{ date('Y') }} GovAssist. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
