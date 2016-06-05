<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Test app CMS" />
        <meta name="author" content="Mojmír Fendek" />
        <title>Error screen</title>
        <style>
            h1 {
                text-align: center;
            }

            a.large_button {
                border: thin solid gray;
                padding: 1ex;
                background-color: gray;
                color: white;
            }
        </style>
    </head>
    <body>
        <h1>Error screen</h1>

        <p style="text-align: center"><?= $message; ?></p>
        <div style="text-align: center"><a class="large_button" href="<?= $home; ?>">Back to home</a></div>
    </body>
</html>
