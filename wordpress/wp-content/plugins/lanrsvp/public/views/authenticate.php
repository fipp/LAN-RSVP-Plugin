<?php
$display = $is_authenticated ? 'none' : 'block';

$is_https = false;
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
    $is_https = true;
}
?>

<div id="lanrsvp-authenticate" style="display:<?php echo $display; ?>;">
	<h2 class="lanrsvp-authenticate-title">Log in</h2>
	<p style="font-size:0.8em;">
        Log in in using your LAN Party Events account to sign up or to unsubscribe to this event.
        If you do not have an account, you can create one below.
    </p>

    <p>
        <a href="#" class="logIn">Log in</a> ::
        <a href="#" class="resetPassword">Reset password</a> ::
        <a href="#" class="resendActivationCode">Resend activation code</a> ::
        <a href="#" class="register">Register new user</a> ::
        <a href="#" class="activate">Activate account</a>
    </p>
    <form class="lanrsvp-login-form">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" autocomplete='off' required /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" autocomplete='off' required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Log in" /></td></tr>
        </table>
    </form>
    <form class="lanrsvp-resetpassword-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" autocomplete='off' required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Reset password"/></td></tr>
        </table>
    </form>
    <form class="lanrsvp-resendactivationcode-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" autocomplete='off' required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Resend activation code"/></td></tr>
        </table>
    </form>
    <form class="lanrsvp-register-form" style="display:none;">
        <table>
            <tr><td>First Name:</td><td><input type="text" name="firstName" autocomplete='off' required /></td></tr>
            <tr><td>Last Name:</td><td><input type="text" name="lastName" autocomplete='off' required /></td></tr>
            <tr><td>E-mail:</td><td><input type="email" name="email" autocomplete='off' required /></td></tr>
            <tr><td>Confirm E-mail:</td><td><input type="email" name="emailConfirm" autocomplete='off' required /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" autocomplete='off' required /></td></tr>
            <tr><td>Confirm Password:</td><td><input type="password" name="passwordConfirm" autocomplete='off' required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Register new user" /></td></tr>
        </table>
    </form>
    <form class="lanrsvp-activate-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" autocomplete='off' required /></td></tr>
            <tr><td>Activation code:</td><td><input type="text" name="activationCode" autocomplete='off' required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Activate" /></td></tr>
        </table>
    </form>
    <p class="lanrsvp-authenticate-message"></p>
</div>