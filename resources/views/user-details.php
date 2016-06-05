<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Test app CMS" />
        <meta name="author" content="Mojmír Fendek" />
        <title><?= $title; ?></title>
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

            input.highlighted {
                border-color: red;
            }
        </style>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript">
            /**
             * @param password
             * @returns {boolean}
             */
            function validatePassword(password)
            {
                // validate length
                if (password.length < 8) {
                    return false;
                }

                var numeric = 0;
                for (var i = 0, len = password.length; i < len; i++) {
                    if (!isNaN(password[i])) {
                        numeric++;
                    }
                }

                // at least 2 numbers are required
                return numeric >= 2;
            }

            /**
             *
             * @param input
             * @param mnessage
             */
            function highlightInputError(input, mnessage)
            {
                input.addClass('highlighted');
                input.focus();
                alert(mnessage)
            }

            $(document).ready(function() {

                // set focus on first name
                $("input[name='first_name']").focus();

                // new password confirmation input handling
                $("button[name='submit_user']").click(function(event){
                    // validate form before submitting

                    // reset highlighted fields
                    $("input[name='first_name']").removeClass('highlighted');
                    $("input[name='last_name']").removeClass('highlighted');
                    $("input[name='email']").removeClass('highlighted');
                    $("input[name='password']").removeClass('highlighted');
                    $("input[name='conf_password']").removeClass('highlighted');

                    // first name
                    if ($("input[name='first_name']").val() == '') {
                        highlightInputError($("input[name='first_name']"), 'First name is missing');
                        return false;
                    }

                    // last name
                    if ($("input[name='last_name']").val() == '') {
                        highlightInputError($("input[name='last_name']"), 'Last name is missing');
                        return false;
                    }

                    // email
                    if ($("input[name='email']").val() == '') {
                        highlightInputError($("input[name='email']"), 'Email name is missing');
                        return false;

                    }
                    // basic email format validation
                    else {
                        var email = $("input[name='email']").val();
                        var atpos = email.indexOf('@');
                        var dotpos = email.lastIndexOf('.');
                        if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= email.length) {
                            highlightInputError($("input[name='email']"), 'Email is invalid');
                            return false;
                        }
                    }


                    <?php if (!empty($force_passwords)) { ?>

                    // passwords mandatory validation (depends on view setup)
                    if ($("input[name='password']").val() == '') {
                        highlightInputError($("input[name='password']"), 'Password is missing');
                        return false;
                    }

                    if ($("input[name='conf_password']").val() == '') {
                        highlightInputError($("input[name='conf_password']"), 'Password confirmation is missing');
                        return false;
                    }

                    <?php } ?>

                    // passwords (optional)
                    if ($("input[name='password']").val() != '' && $("input[name='conf_password']").val() != '') {
                        if ($("input[name='password']").val() != $("input[name='conf_password']").val()) {
                            $("input[name='conf_password']").addClass('highlighted');
                            highlightInputError($("input[name='password']"), 'Passwords need to be the same');
                            return false;
                        }

                        if (!validatePassword($("input[name='password']").val())) {
                            highlightInputError($("input[name='password']"), 'Password needs to have at least 8 characters and 2 of them numbers');
                            return false;
                        }

                        if (!validatePassword($("input[name='conf_password']").val())) {
                            highlightInputError($("input[name='conf_password']"), 'Password confirmation needs to have at least 8 characters and 2 of them numbers');
                            return false;
                        }
                    }

                    return true;
                });
            });
        </script>
    </head>
    <body>
        <h1><?= $title; ?></h1>
        <form action="<?= $create_url; ?>" method="post">

        <div style="text-align: center">
            <a class="large_button" href="<?= $back_url; ?>">Back</a>
        </div>

        <p>First name</p>
        <p><input type="text" name="first_name" title="First name" maxlength="80" value="<?= $data['first_name']; ?>" /></p>
        <p>Last name</p>
        <p><input type="text" name="last_name" title="Last name" maxlength="80" value="<?= $data['last_name']; ?>" /></p>
        <p>Email</p>
        <p><input type="text" name="email" title="Email" maxlength="50" value="<?= $data['email']; ?>" /></p>
        <p>Password</p>
        <p><input type="password" name="password" title="Password" maxlength="16" /></p>
        <p>Confirm password</p>
        <p><input type="password" name="conf_password" title="Confirm password" maxlength="16" /></p>
        <?php if (!empty($show_admin_data)) { ?>
            <p>Group</p>
            <p>
                <select name="group" title="Group">
                    <?php foreach (['user', 'admin'] as $field) { ?>
                        <option value="<?= $field; ?>"<?= ($data['group'] == $field) ? '" selected="selected"' : ''; ?>><?= $field; ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>Active</p>
            <p><input type="checkbox" name="active" title="Active"<?= ($data['active']) ? '" checked="checked"' : ''; ?> /></p>
        <?php } ?>
        <?php if (!empty($display_fb_id)) { ?>
            <p>Facebook id: <?= $data['fb_id']; ?></p>
        <?php } ?>

        <div style="text-align: center">
            <button type="submit" name="submit_user"><?= $label; ?></button>
        </div>
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        </form>
    </body>
</html>
