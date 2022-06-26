<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet SDK</title>
    <link rel="stylesheet" href="./../dist/main.css">
</head>
<body>

    <main class="login">
        <article>
            <header>
                <h1>Project SDK</h1>
                <h3>Login in to your account</h3>
            </header>
            <article>
                <form action='callback' method='POST'>
                    <input type='text' name='username' placeholder='Username'>
                    <input type='password' name='password' value="test" placeholder='Password'>
                    <input type='submit' value='Login in'>
                </form>
            </article>
            <div id="separator">
                <span>Or</span>
            </div>
            <footer>
            <?php
                $oauth = App\Model\Oauth::getInstance();

                foreach ($oauth->getProviders() as $provider) {
                ?>
                    
                <a href="<?= $provider->getAuthUrl() ?>">
                    <?php if (getIconsVerif($provider->getName())): ?>

                    <?= getIcon($provider->getName()); ?>
                    <span>Sign in with <?= ucfirst($provider->getName()) ?></span>

                    <?php else: ?>

                    <span>Sign in with <?= ucfirst($provider->getName()) ?></span>

                    <?php endif ?>
                </a>
                <?php
                }
                ?>
            </footer>
        </article>
    </main>
    
</body>
</html>