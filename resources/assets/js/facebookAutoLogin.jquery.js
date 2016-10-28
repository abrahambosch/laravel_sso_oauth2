/**
 * Created by abrahambosch on 10/27/16.
 */


(function( $ ) {
    var facebookAutoLogin = {
        options: {
            client_id: ''
        }, // empty by default

        init: function (options) {
            var self=this;
            if (self._isObject(options)) {
                jQuery.extend(self.options, options);
            }


            console.log("options received = ", self.options);
            window.fbAsyncInit = function () {
                self.fbAsyncInit();
            };  // register the callback.

            // Load the Facebook SDK asynchronously
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

        },
        _isObject: function (obj) {  // helpers start with underscore.
            return (typeof obj ==  'object' && obj instanceof Object);
        },
        // This is called with the results from from FB.getLoginStatus().
        statusChangeCallback: function (response) {
            var self=this;
            console.log('statusChangeCallback');
            console.log(response);
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                //self.getUserProfile();
                self.signinFacebookUser(response.authResponse.signedRequest);
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into this app.';
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into Facebook.';
            }
        },
        // This function is called when someone finishes with the Login
        // Button.  See the onlogin handler attached to it in the sample
        // code below.
        checkLoginState: function () {
            var self=this;
            FB.getLoginStatus(function(response) {
                self.statusChangeCallback(response);
            });
        },

        signinFacebookUser: function (signedRequest) {    // same thing as above
            var self=this;
            jQuery.getJSON('/auth/facebook/signinFacebookUser', {
                signed_request: signedRequest
            }).done(function(response){
                console.log("got response back from signinFacebookUser", response);

                if (response.status) {
                    window.location.href='/';
                }
            })
        },
        fbAsyncInit: function() {
            var self=this;
            console.log("in fbAsyncInit, self.options=", self.options);
            FB.init({
                appId      : self.options.client_id,
                cookie     : true,  // enable cookies to allow the server to access
                                    // the session
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.5' // use graph api version 2.5
            });

            // Now that we've initialized the JavaScript SDK, we call
            // FB.getLoginStatus().  This function gets the state of the
            // person visiting this page and can return one of three states to
            // the callback you provide.  They can be:
            //
            // 1. Logged into your app ('connected')
            // 2. Logged into Facebook, but not your app ('not_authorized')
            // 3. Not logged into Facebook and can't tell if they are logged into
            //    your app or not.
            //
            // These three cases are handled in the callback function.

            FB.getLoginStatus(function(response) {
                self.statusChangeCallback(response);
            });

        },

        // Here we run a very simple test of the Graph API after login is
        // successful.  See statusChangeCallback() for when this call is made.
        getUserProfile: function () {
            console.log('Welcome!  Fetching your information.... ');
            FB.api('/me', function(response) {
                console.log("response from facebook", response);
                console.log('Successful login for: ' + response.name);
                document.getElementById('status').innerHTML =
                    'Thanks for logging in, ' + response.name + '!';


            });
        }
    };

    $.fn.facebookAutoLogon = function( options ) {
        facebookAutoLogin.init(options);
    };

}( jQuery ));