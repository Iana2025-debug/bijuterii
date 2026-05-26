<?php

$message = 'Salut! Acesta este un mesaj afișat atât în consolă, cât și în aplicația web.';

// Pentru rulare din CLI
if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
    fwrite(STDOUT, $message . PHP_EOL);
    exit(0);
}

// Pentru afișare în browser
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Mesaj PHP</title>
</head>
<body>
    <h1>Mesaj PHP</h1>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
</body>
</html>
