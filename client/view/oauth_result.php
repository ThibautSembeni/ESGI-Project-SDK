<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet - SDK | Login Sucess</title>
</head>
<body>
    <h1>Login result</h1>
    <?php

        $oauth = App\Model\Oauth::getInstance();

        if (empty($_SESSION)) 
        {
            header('Location: /' );
        }
        echo $oauth->getUser();
        echo $oauth->getToken();
    ?>
</body>
</html>