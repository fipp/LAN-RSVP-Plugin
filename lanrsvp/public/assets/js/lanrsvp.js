(function ( $ ) {
	"use strict";

	$(function () {

        $('div#lanrsvp-authenticate a').click(function(e) {
            e.preventDefault();
            var className = $(this).attr('class');
            $('div#lanrsvp-authenticate > form').hide();
            switch (className) {
                case 'logIn':
                    $('h2.lanrsvp-authenticate-title').text('Log in');
                    $('form.lanrsvp-login-form').show();
                    break;
                case 'resetPassword':
                    $('h2.lanrsvp-authenticate-title').text('Reset password');
                    $('form.lanrsvp-resetpassword-form').show();
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
                    $('div.lanrsvp-authenticated-message').html(msg);
                    return;
                }
            }

            $.post(LanRsvp.ajaxurl, data, function (response) {
                if (response.length > 0) {
                    $('div.lanrsvp-authenticated-message').html(response);
                } else {
                    data = {
                        action: 'get_authenticated',
                        event_id: LanRsvp.event_id,
                        has_seatmap: LanRsvp.has_seatmap
                    };

                    $.post(LanRsvp.ajaxurl, data, function (response) {
                        $('div#lanrsvp-authenticated').replace(response);
                        $('div#lanrsvp-authenticated').show();
                    });

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
                    $('div.lanrsvp-authenticate-message').html(response);
                } else {
                    $('div#lanrsvp-authenticate').hide();

                    data = {
                        action: 'get_authenticated',
                        event_id : LanRsvp.event_id,
                        has_seatmap : LanRsvp.has_seatmap
                    };

                    $.post( LanRsvp.ajaxurl, data, function(response) {
                        $('div#lanrsvp-authenticated').replace(response);
                        $('div#lanrsvp-authenticated').show();
                    });
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
                    $('div.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Registration successful. Check your e-mail account to activate your account.</p>";
                    $('div.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate > form').hide();
                    $('form.lanrsvp-login-form').show();
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
                    $('div.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Registration successful. Check your e-mail account to activate your account.</p>";
                    $('div.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate > form').hide();
                    $('form.lanrsvp-login-form').show();
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
                    $('div.lanrsvp-authenticate-message').html(response);
                } else {
                    var html = "<p>Activation successful. Please log in.</p>";
                    $('div.lanrsvp-authenticate-message').html(html);
                    $('div#lanrsvp-authenticate > form').hide();
                    $('form.lanrsvp-login-form').show();
                }
            });
        }



    });

}(jQuery));