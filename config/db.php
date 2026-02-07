<?php
session_start();

$conn = new mysqli("localhost","root","","village_shop");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("DB Error");
}
