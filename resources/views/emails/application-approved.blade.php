<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Approved</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f9fc; margin: 0; padding: 40px 0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border: 1px solid #e8e8e8; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <tr>
            <td align="center" bgcolor="#b91c1c" style="padding: 30px 0 20px 0; color: #ffffff; font-size: 24px; font-weight: bold; letter-spacing: 1px;">
                GovAssist
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px; line-height: 1.6; color: #333333;">
                <h2 style="color: #1e293b; margin-top: 0;">Application Approved!</h2>
                <p>Hello <strong>{{ $checklist->user->name }}</strong>,</p>
                <p>Great news! Your application for <strong>{{ $checklist->service->name_en }}</strong> has been approved by the admin.</p>
                <p style="background-color: #fef2f2; border-left: 4px solid #b91c1c; padding: 15px; margin: 20px 0; font-size: 14px; color: #7f1d1d;">
                    <strong>Next Steps:</strong> You may now proceed or go to the <strong>San Miguel Sub Office</strong> for Signature and Submission of the paper and for other instructions.
                </p>
                <p>Please make sure to bring all required documents that you uploaded for verification.</p>
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
