<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}
</style>
</head>
<body>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                {{ $header ?? '' }}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                                    {{ $subcopy ?? '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" role="presentation" width="570" align="center">
                                <tr></tr>
                                <tr>
                                    <td>
                                        <p style="font-size: 8px; font-style: italic; justify-content: center;"><b>CONFIDENTIAL</b>: This email and any files transmitted with it are confidential and intended solely for the use of
                                        the individual or entity to whom they are addressed. If you have received this email in error please notify the
                                        system manager. This message contains confidential information and is intended only for the individual named.
                                        If you are not the named addressee you should not disseminate, distribute or copy this email. Please notify the
                                        sender immediately by email if you have received this email by mistake and delete this email from your system.
                                        If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any
                                        action in reliance on the contents of this information is strictly prohibited.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                {{ $footer ?? '' }}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
