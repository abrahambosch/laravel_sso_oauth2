/**
 * Created by abrahambosch on 10/27/16.
 */


(function( $ ) {

    var _facebookSdk = {
        options: {
            client_id: '',
            auto_login: true,    // set to false if you want to just check if they are able to login.
            console_log: false
        }, // empty by default


        /**
         * when this resolves, the Facebook SDK will be loaded and the FB object will be available.
         * @param options
         * @returns {*}
         */
        init: function (options) {
            var self=this;

            self.facebookSdkDeferred = $.Deferred();

            if (self._isObject(options)) {
                jQuery.extend(self.options, options);
            }


            if (self.options.console_log) console.log("options received = ", self.options);
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

            return self.facebookSdkDeferred.promise();
        },

        promise: function() {   // this returns the promise for the main deferred object.
            return this.facebookSdkDeferred.promise();
        },
        _isObject: function (obj) {  // helpers start with underscore.
            return (typeof obj ==  'object' && obj instanceof Object);
        },
        // This is called with the results from from FB.getLoginStatus().
        getLoginStatusCallback: function (response) {
            var self=this;
            self.checkLoginStateResponse = response;
            if (self.options.console_log) console.log('getLoginStatusCallback');
            if (self.options.console_log) console.log(response);

            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                //self.getUserProfile();

                if (self.options.auto_login) {
                    self.signinFacebookUser(response.authResponse.signedRequest).done(function(){
                        self.checkLoginStateDeferred.resolve(response);
                    }).fail(function(){
                        self.checkLoginStateDeferred.reject(response);
                    });
                }
                else {
                    self.checkLoginStateDeferred.resolve(response);
                }
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into this app.';
                self.checkLoginStateDeferred.reject(response.status);
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into Facebook.';
                self.checkLoginStateDeferred.reject(response.status);
            }

        },

        /**
         * Auto Log user into facebook by checkLoginState() and then signinFacebookUser()
         * @returns promise
         */
        autoLogin: function() {
            var self=this, d = $.Deferred();
            self.checkLoginState().done(function(response){
                if (response.status == 'connected') {
                    self.signinFacebookUser(response.authResponse.signedRequest).done(function(){
                        d.resolve(response);
                    }).fail(function(){
                        d.reject(response.status);
                    });
                }
                else {
                    d.reject(response.status);
                }

            }).fail(function(error){
                d.reject(error);
            });
            return d.promise();
        },

        // This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.
        checkLoginState: function () {
            var self=this;
            self.checkLoginStateDeferred = $.Deferred();
            if (self.options.console_log) console.log("in function checkLoginState");
            self.promise().done(function(){ // make sure main library is loaded before doing this.
                if (self.options.console_log) console.log("in function checkLoginState, main lib loaded. calling FB.getLoginStatus");
                FB.getLoginStatus(function(response) {
                    if (self.options.console_log) console.log("in function checkLoginState, FB.getLoginStatus returned ", response);
                    self.checkLoginStateResponse = response;
                    self.checkLoginStateDeferred.resolve(response);
                    //self.getLoginStatusCallback(response);
                });
            });
            return self.checkLoginStateDeferred.promise();
        },

        signinFacebookUser: function (signedRequest) {    // same thing as above
            var self=this, d = $.Deferred();
            if (typeof signedRequest == 'undefined' && typeof self.checkLoginStateResponse != 'undefined') {
                signedRequest = self.checkLoginStateResponse.authResponse.signedRequest;
            }
            if (typeof signedRequest == 'undefined') {
                d.reject("a facebook signedRequest is required for the signinFacebookUser function .");
            }
            promise = jQuery.getJSON('/auth/facebook/signinFacebookUser', {
                signed_request: signedRequest
            }).done(function(response){
                if (self.options.console_log) console.log("got response back from signinFacebookUser", response);

                if (response.status) {
                    //window.location.href='/';
                    d.resolve();
                }
                else {
                    d.reject(response.message);
                }
            }).fail(function(jqxhr, textStatus, error){
                var err = textStatus + ", " + error;
                if (self.options.console_log) console.log( "Request Failed: " + err );
                d.reject(err);
            });
            return d.promise();
        },
        fbAsyncInit: function() {
            var self=this;
            if (self.options.console_log) console.log("in fbAsyncInit, self.options=", self.options);
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

            //self.checkLoginState();
            // FB.getLoginStatus(function(response) {
            //     self.getLoginStatusCallback(response);
            // });

            self.facebookSdkDeferred.resolve(FB);

        },

        // Here we run a very simple test of the Graph API after login is
        // successful.  See getLoginStatusCallback() for when this call is made.
        getUserProfile: function () {
            var self=this;
            if (self.options.console_log) console.log('Welcome!  Fetching your information.... ');
            FB.api('/me', function(response) {
                if (self.options.console_log) console.log("response from facebook", response);
                if (self.options.console_log) console.log('Successful login for: ' + response.name);
                document.getElementById('status').innerHTML =
                    'Thanks for logging in, ' + response.name + '!';


            });
        }
    };

    function FacebookSdkHelper(options) {
        this.init(options);
    };
    FacebookSdkHelper.prototype = Object.create(_facebookSdk);

    window.FacebookSdkHelper = FacebookSdkHelper;

    $.fn.facebookAutoLogin = function( options ) {
        var facebookSdkHelperObj = new FacebookSdkHelper(options);
        this.each(function() {
            // Do something to each element here.
            $(this).data("facebookAutoLogin", facebookSdkHelperObj);   // attach the object for futher reference.
        });

        if (options.auto_login) {
            return facebookSdkHelperObj.autoLogin();
        }
        else {
            return facebookSdkHelperObj.checkLoginState();
        }
    };

}( jQuery ));