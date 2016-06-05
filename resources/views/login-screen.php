<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Test app CMS" />
        <meta name="author" content="Mojmír Fendek" />
        <title>Login screen</title>
        <style>
            /* heading */
            h1 {
                text-align: center;
            }

            button, a.large_button {
                font-family: Arial, Helvetica, sans-serif;
                font-size: small;
                border: thin solid gray;
                padding: 1ex;
                background-color: gray;
                text-decoration: none;
                color: white;
                cursor: default;
            }

            p {
                text-align: center;
            }
        </style>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                // set focus on email
                $("input[name='email']").focus();
            });
        </script>
    </head>
    <body>
        <script>
            function statusChangeCallback(response) {
//                console.log('statusChangeCallback');
//                console.log(response);
                if (response.status === 'connected') {
//                    console.log('Welcome!  Fetching your information.... ');
                    FB.api('/me', {fields: 'id,first_name,last_name'}, function(response) {
                        var login_url = '<?= $fb_login_url; ?>' + '?id=' + response.id + '&first_name=' + response.first_name + '&last_name=' + response.last_name;
                        window.location.replace(login_url);
                    });
                } else if (response.status === 'not_authorized') {
                    alert('Please log into this app.');
                } else {
                    alert('Please log into Facebook.');
                }
            }

            function checkLoginState() {
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });
            }

            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '1212977325421779',
                    cookie     : true,  // enable cookies to allow the server to access
                                        // the session
                    xfbml      : true,  // parse social plugins on this page
                    version    : 'v2.6' // use graph api version 2.5
                });


//                FB.getLoginStatus(function(response) {
//                    statusChangeCallback(response);
//                });

            };

            // Load the SDK asynchronously
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <h1>Login screen</h1>
        <form action="<?= $login_url; ?>" method="post">

        <p>Email</p>
        <p><input type="text" name="email" title="Email" maxlength="50" /></p>
        <p>Password</p>
        <p><input type="password" name="password" title="Password" maxlength="16" /></p>

        <div style="text-align: center">
            <div class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="false" data-auto-logout-link="false" onlogin="checkLoginState();"></div>
            <button type="submit" name="login">Login</button>
            <a class="large_button" href="<?= $register_url; ?>">Register</a>
        </div>
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        </form>
    </body>
</html>
