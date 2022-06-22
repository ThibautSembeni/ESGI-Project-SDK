<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet SDK</title>
    <style>

        * {
            background-color: black;
            color: white;
        }

    </style>
</head>
<body>
    <h1>Projet SDK</h1>
    <?php
    foreach ($oauth->getProviders() as $provider) {
    ?>
        <ul>
			<li>
                <a href="<?= $provider->getAuthUrl() ?>">
                <?php if (getIconsVerif($provider->getName())): ?>

                <?= getIcon($provider->getName()); ?>

                <?php else: ?>

                <?= $provider->getName() ?>

                <?php endif ?>
             </a></li>
		</ul>
    <?php
    }
    ?>
</body>
</html>