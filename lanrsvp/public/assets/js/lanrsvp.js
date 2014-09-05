(function ( $ ) {
	"use strict";

	$(function () {

        $('a.logIn').click(function(e) {
            e.preventDefault();
            $('div.lanrsvp-user > form').hide('fast');
            $('form.lanrsvp-login-form').show('fast');
        });

        $('a.resetPassword').click(function(e) {
            e.preventDefault();
            $('div.lanrsvp-user > form').hide('fast');
            $('form.lanrsvp-resetpassword-form').show('fast');
        });

        $('a.register').click(function(e) {
            e.preventDefault();
            $('div.lanrsvp-user > form').hide('fast');
            $('form.lanrsvp-register-form').show('fast');
        });

        $('a.activate').click(function(e) {
            e.preventDefault();
            $('div.lanrsvp-user > form').hide('fast');
            $('form.lanrsvp-activate-form').show('fast');
        });

        $('form.lanrsvp-login-form').submit(function(e) {
            e.preventDefault();
            doLogin($(this));
        });

        $('form.lanrsvp-resetpassword-form').submit(function(e) {
            e.preventDefault();
            doResetPassword($(this));
        });

        $('form.lanrsvp-register-form').submit(function(e) {
            e.preventDefault();
            doRegister($(this));
        });

        $('form.lanrsvp-activate-form').submit(function(e) {
            e.preventDefault();
            doActivate($(this));
        });

        function doLogin(form) {
            var data = {
                action: 'login',
                email: form.find('input[name="email"]').val(),
                password: form.find('input[name="password"]').val()
            };

            $.post( LanRsvp.ajaxurl, data, function(response) {
                if (response.length > 0) {
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(response);
                } else {
                    $('div.lanrsvp-user').hide('fast');
                    $('div.lanrsvp-actions').show('fast');
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
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(response);
                } else {
                    var html = "<p>Please check your email to get your new password..</p>";
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(html);
                    $('div.lanrsvp-user > form').hide('fast');
                    $('form.lanrsvp-login-form').show('fast');
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
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(response);
                } else {
                    var html = "<p>Registration successful. Check your e-mail account to activate your account.</p>";
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(html);
                    $('div.lanrsvp-user > form').hide('fast');
                    $('form.lanrsvp-activate-form').show('fast');
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
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(response);
                } else {
                    var html = "<p>Activation successful. Please log in.</p>";
                    $('div.lanrsvp-user > div.lanrsvp-user-message').html(html);
                    $('div.lanrsvp-user > form').hide('fast');
                    $('form.lanrsvp-login-form').show('fast');
                }
            });
        }

    });

}(jQuery));

function doLogin (formElement) {
    var ajaxParameters = {

    };

    jQuery.get( ajaxUrl, ajaxParameters, successHandler );
}

function successHandler (data) {
    jQuery( ".lanrsvp" ).html( data );
}
