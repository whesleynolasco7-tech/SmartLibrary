<?php

$url = "https://www.googleapis.com/books/v1/volumes?q=harry+potter";

$response = file_get_contents($url);

if ($response === false) {
    die("Connection Failed");
}

echo "<pre>";
print_r(json_decode($response, true));