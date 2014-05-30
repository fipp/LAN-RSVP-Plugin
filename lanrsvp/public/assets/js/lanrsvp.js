(function ( $ ) {
	"use strict";

	$(function () {

        $('form.lanrsvp-login').submit(function(e) {
            e.preventDefault();
            doLogin($(this));
        });

	});

    // var exampleObject = new parentFunction({ privateVar : 'new Value' });
    // exampleObject.childFunction();

}(jQuery));

function doLogin (formElement) {
    var ajaxParameters = {
        email : formElement.find('input[type="email"]').val(),
        password : formElement.find('input[type="password"]').val(),
        action : 'login'
    };

    jQuery.get( ajaxUrl, ajaxParameters, successHandler );
}

function successHandler (data) {
    jQuery( ".lanrsvp" ).html( data );
}



/*
var parentFunction = function(options){
    var vars = {
        privateVar : 'original Value',
        another: 'test'
    }

    var root = this;

    this.construct = function(options){
        $.extend(vars , options);
    }

    this.childFunction = function(){
        alert(vars.privateVar);
    }

    this.construct(options);
}
*/