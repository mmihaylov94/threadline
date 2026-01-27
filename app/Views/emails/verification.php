<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email – Threadline</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; background-color: #f6f7fb;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f6f7fb;">
        <tr>
            <td style="padding: 32px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="padding: 32px 28px;">
                            <h1 style="margin: 0 0 16px; font-size: 1.5rem; font-weight: 700; color: #1f2937;">Welcome to Threadline</h1>
                            <p style="margin: 0 0 12px; color: #374151;">Hi <?= esc($username) ?>,</p>
                            <p style="margin: 0 0 24px; color: #374151;">Thank you for registering. Please verify your email address by clicking the button below:</p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="border-radius: 8px; background: #3B82F6;">
                                        <a href="<?= esc($verificationLink) ?>" style="display: inline-block; padding: 12px 24px; font-weight: 600; color: #ffffff !important; text-decoration: none;">Verify email address</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 24px 0 8px; font-size: 14px; color: #6b7280;">Or copy and paste this link into your browser:</p>
                            <p style="margin: 0 0 24px; font-size: 14px; word-break: break-all; color: #6b7280;"><?= esc($verificationLink) ?></p>
                            <p style="margin: 0; font-size: 14px; color: #6b7280;">This link expires in 24 hours.</p>
                            <p style="margin: 16px 0 0; font-size: 14px; color: #6b7280;">If you didn't create an account, you can safely ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
