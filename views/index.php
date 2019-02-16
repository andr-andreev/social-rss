<?php
declare(strict_types=1);
/** @var \SocialRss\Parser\ParserInterface[] $parsers */

/** @var \Slim\Router $router */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Social RSS</title>
</head>
<body>
<h1>Welcome to Social RSS!</h1>

<h2>View <em>your</em> timeline as RSS</h2>
<ul>
    <?php foreach ($parsers as $parserName => $parserClassName):
        /** @var \SocialRss\Parser\ParserInterface $parserClassName */
        $name = $parserClassName::getName();
        $link = $router->pathFor('parser', ['source' => $parserName]);
        ?>
        <li>
            <?= $name ?>: <a href="<?= $link ?>"><?= $link ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Change output format and view <em>any particular user</em> timeline</h2>
<p>
    See <a href="https://github.com/andr-andreev/social-rss/blob/master/README.md#usage">usage docs</a>.
</p>

</body>
</html>
