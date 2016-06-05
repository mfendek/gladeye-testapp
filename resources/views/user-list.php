<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Test app CMS" />
        <meta name="author" content="Mojmír Fendek" />
        <title>Users - list</title>
        <style>
            /* heading */
            h1 {
                text-align: center;
            }

            /* table borders and highlights */
            table {
                margin: 1em auto 1em auto;
                border-top: thin solid black;
                border-right: thin solid black;
                border-left: thin solid black;
            }

            table th, table td {
                text-align: center;
                padding: 1ex;
                border-bottom: thin solid black;
            }

            table th + th, table td + td {
                border-left: thin solid black;
            }

            table tr:hover > td {
                color: white;
                background-color: gray;
            }

            /* link buttons */
            a.small_button, a.large_button {
                text-decoration: none;
                cursor: default;
            }

            a.small_button {
                border: thin solid black;
                padding: 0.5ex;
                background-color: white;
                color: black;
            }

            a.large_button {
                border: thin solid gray;
                padding: 1ex;
                background-color: gray;
                color: white;
            }
        </style>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                // delete user confirmation
                $("a.delete_button").click(function() {
                    if (confirm("Are you sure you want to delete this user?")) {
                        return true;
                    }
                    else {
                        return false;
                    }
                });
            });
        </script>
    </head>
    <body>
        <h1>Users list</h1>

        <div style="text-align: center">
            <?php if ($advanced_access) { ?>
                <a class="large_button" href="<?= $add_url; ?>">Add user</a>
            <?php } ?>
            <a class="large_button" href="<?= $logout_url; ?>">Logout</a>
        </div>

        <?php if (count($users) > 0) { ?>
        <table cellspacing="0">
            <tr>
                <?php
                    $fields = ['First name', 'Last name', 'Email', 'Active', 'Facebook id', 'Created'];
                    if ($advanced_access) {
                        $fields[]= '';
                    }
                ?>
                <?php foreach ($fields as $field) { ?>
                    <th><?= $field; ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <?php foreach (['first_name', 'last_name', 'email', 'active', 'fb_id', 'created_at'] as $field) { ?>
                        <td><?= $user[$field]; ?></td>
                    <?php } ?>
                    <?php if ($advanced_access) { ?>
                        <td>
                            <a class="small_button" href="<?= $edit_url.'/'.$user['id']; ?>">Edit</a>
                            <a class="small_button delete_button" href="<?= $delete_url.'/'.$user['id']; ?>">Delete</a>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </body>
</html>
