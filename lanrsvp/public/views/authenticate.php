<?php
$display = $is_authenticated ? 'none' : 'block';
?>

<div id="lanrsvp-authenticate" style="display:<?php echo $display; ?>;">
	<h2 class="lanrsvp-authenticate-title">Log in</h2>
	<p>To register or change your registration, you have to log in in using your LAN RSVP System account:</p>

    <div class="lanrsvp-authenticate-message"></div>

    <form class="lanrsvp-login-form">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Log in" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="register">Register new user</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-resetpassword-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Reset password"/></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="register">Register new user</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-register-form" style="display:none;">
        <table>
            <tr><td>First Name:</td><td><input type="text" name="firstName" required value="Terje Ness"/></td></tr>
            <tr><td>Last Name:</td><td><input type="text" name="lastName" required value="Andersen" /></td></tr>
            <tr><td>E-mail:</td><td><input type="email" name="email" required value="terje.andersen@gmail.com" /></td></tr>
            <tr><td>Confirm E-mail:</td><td><input type="email" name="emailConfirm" required value="terje.andersen@gmail.com" /></td></tr>
            <tr><td>Password:</td><td><input type="password" name="password" required value="foobar" /></td></tr>
            <tr><td>Confirm Password:</td><td><input type="password" name="passwordConfirm" required value="foobar" /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Register" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="activate">Activate account</a>
                </td>
            </tr>
        </table>
    </form>
    <form class="lanrsvp-activate-form" style="display:none;">
        <table>
            <tr><td>E-mail:</td><td><input type="email" name="email" required /></td></tr>
            <tr><td>Activation code:</td><td><input type="text" name="activationCode" required /></td></tr>
            <tr><td colspan="2"><input type="submit" value="Activate" /></td></tr>
            <tr>
                <td colspan="2">
                    <a href="#" class="logIn">Log in</a> -
                    <a href="#" class="resetPassword">Reset password</a> -
                    <a href="#" class="register">Register new user</a>
                </td>
            </tr>
        </table>
    </form>
</div>