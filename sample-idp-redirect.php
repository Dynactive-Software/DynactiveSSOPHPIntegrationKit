<?php

if (isset($_GET['status']) && $_GET['status'] == "OK") {
    $redirect = $_GET['redirect'];
    header("Location: $redirect");
    exit();
}
else {
    var_dump($_GET);
}