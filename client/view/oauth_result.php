<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet - SDK | Login Sucess</title>
    <link rel="stylesheet" href="./../dist/main.css">
</head>
<body>

    <main class="oauth-success">
        <section>
            <header>
                <h1>Login result</h1>
            </header>
            <article>
                <?php

                $oauth = App\Model\Oauth::getInstance();

                if (empty($_SESSION)) 
                {
                    header('Location: /' );
                } 
                ?>
                <h3>User :</h3>
                <span><?= $oauth->getUser() ?></span>  
                <h3>Acess Token :</h3> 
                <span><?= $oauth->getToken() ?></span>
            </article>
            <footer>
                <a href="/">Try another connexion Oauth</a>
            </footer>
        </section>
    </main>   
</body>
</html>