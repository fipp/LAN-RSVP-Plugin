(function ( $ ) {
	"use strict";

	$(function () {

        $('div#lanrsvp-authenticate a').click(function(e) {
            var className = $(this).attr('class');
            if (className.length > 0) {
                e.preventDefault();
            } else {
                return;
            }

            $('div#lanrsvp-authenticate form').hide();
            $('p.lanrsvp-authenticate-message').empty();

            switch (className) {
                case 'logIn':
                    $('h2.lanrsvp-authenticate-title').text('Log in');
                    $('form.lanrsvp-login-form').show();
                    break;
                case 'resetPassword':
                    $('h2.lanrsvp-authenticate-title').text('Reset password');
                    $('form.lanrsvp-resetpassword-form').show();
                    break;
                case 'resendActivationCode':
                    $('h2.lanrsvp-authenticate-title').text('Resend activation code');
                    $('form.lanrsvp-resendactivationcode-form').show();
                    break;
                case 'register':
                    $('h2.lanrsvp-authenticate-title').text('Register new user');
                    $('form.lanrsvp-register-form').show();
                    break;
                case 'activate':
                    $('h2.lanrsvp-authenticate-title').text('Activate account');
                    $('form.lanrsvp-activate-form').show();
                    break;
            }
        });

        $('div#lanrsvp-authenticate form').submit(function(e) {
            e.preventDefault();
            var className = $(this).attr('class');
            switch (className) {
                case 'lanrsvp-login-form':
                    doLogin($(this));
                    break;
                case 'lanrsvp-resetpassword-form':
                    doResetPassword($(this));
                    break;
                case 'lanrsvp-resendactivationcode-form':
                    doResendActivationCode($(this));
                    break;
                case 'lanrsvp-register-form':
                    doRegister($(this));
                    break;
                case 'lanrsvp-activate-form':
                    doActivate($(this));
                    break;
            }
        });

        $('button.signUp').click(function() {
            var data = {
                action: 'sign_up',
                event_id: LanRsvp['event_id'],
                seat_row: '-',
                seat_col: '-'
            };

            if (LanRsvp['has_seatmap'] === '1') {
                var chosenSeat = window.chosenSeat;
                if (chosenSeat !== undefined && chosenSeat[0] !== undefined && chosenSeat[1] !== undefined) {
                    data['seat_row'] = chosenSeat[0];
                    data['seat_col'] = chosenSeat[1];
                } else {
                    var msg = "No seat chosen, please try again.";
                    $('p.lanrsvp-authenticated-message').html(msg);
                    return;
                }
            }

            $.post(LanRsvp.ajaxurl, data, function (response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticated-message').html(response);
                } else {
                    var isHardRefresh = false;
                    location.reload(isHardRefresh);
                }
            });
        });

        $('button.logOut').click(function() {
            $.post(LanRsvp.ajaxurl, {action: 'logout'}, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticated-message').html(response);
                } else {
                    var isHardRefresh = false;
                    location.reload(isHardRefresh);
                }
            });
        });

        $('button.unsubscribe').click(function() {
            var data = {
                action: 'unsubscribe',
                event_id: LanRsvp['event_id']
            };
            $.post(LanRsvp.ajaxurl, data, function (response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticated-message').html(response);
                } else {
                    var isHardRefresh = false;
                    location.reload(isHardRefresh);
                }
            });
        });

        function doLogin(form) {
            var data = {
                action: 'login',
                email: form.find('input[name="email"]').val(),
                password: form.find('input[name="password"]').val()
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticate-message').html(response);
                } else {
                    var isHardRefresh = false;
                    location.reload(isHardRefresh);
                }
            });
        }

        function doResetPassword(form) {
            var data = {
                action: 'reset_password',
                email: form.find('input[name="email"]').val()
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Password reset successfully. Please check your e-mail.</p>";
                    $('p.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate form').hide();

                    $('form.lanrsvp-login-form').find('input[type=text], textarea').val('');
                    $('form.lanrsvp-login-form').find('input[name="email"]').val(data['email']);
                    $('form.lanrsvp-login-form').show();
                }
            });
        }

        function doResendActivationCode(form) {
            var email = form.find('input[name="email"]').val()

            var data = {
                action: 'resend_activationcode',
                email: email
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Activation code sent. Please check your e-mail.</p>";
                    $('p.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate form').hide();

                    $('form.lanrsvp-activate-form').find('input[type=text], textarea').val('');
                    $('form.lanrsvp-activate-form').find('input[name="email"]').val(email);
                    $('form.lanrsvp-activate-form').show();
                }
            });
        }

        function doRegister(form) {
            var firstName = form.find('input[name="firstName"]').val();
            var lastName = form.find('input[name="lastName"]').val();
            var email = form.find('input[name="email"]').val();
            var emailConfirm = form.find('input[name="emailConfirm"]').val();
            var password = form.find('input[name="password"]').val();
            var passwordConfirm = form.find('input[name="passwordConfirm"]').val();

            if (email !== emailConfirm) {
                alert("E-mails not matching, try again!");
                return;
            }

            if (password !== passwordConfirm) {
                alert("Passwords are not matching, try again!");
                return;
            }

            var data = {
                action: 'register',
                firstName: firstName,
                lastName: lastName,
                email: email,
                emailConfirm: emailConfirm,
                password: password,
                passwordConfirm: passwordConfirm
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Registration successful. Check your email in order to activate your account.</p>";
                    $('p.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate form').hide();

                    $('form.lanrsvp-activate-form').find('input[type=text], textarea').val('');
                    $('form.lanrsvp-activate-form').find('input[name="email"]').val(email);
                    $('form.lanrsvp-activate-form').show();
                }
            });
        }

        function doActivate(form) {
            var data = {
                action: 'activate_user',
                email: form.find('input[name="email"]').val(),
                activationCode: form.find('input[name="activationCode"]').val()
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('p.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Activation successful. Please log in.</p>";
                    $('p.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate form').hide();

                    $('form.lanrsvp-login-form').find('input[type=text], textarea').val('');
                    $('form.lanrsvp-login-form').find('input[name="email"]').val(data['email']);
                    $('form.lanrsvp-login-form').show();
                }
            });
        }



    });

}(jQuery));