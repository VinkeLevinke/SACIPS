<?php
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
    echo "<h1>Error</h1>";
    echo "<pre>$error</pre>";
} else {
    echo "<h1>No se proporcionó ningún error.</h1>";
}
?>
