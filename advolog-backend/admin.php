<?php
$response;
function respond($message)
{
    $response["response"] = $message;
    echo json_encode($response);
}

$db = mysqli_connect("localhost", "root", "", "advolog");
if (!$db) die(respond("CONNECTION_ERROR"));

if (!(isset($_GET["e"]) && isset($_GET["p"]))) die(respond("INVALID_REQUEST"));

$e = $_GET["e"];
$p = password_hash($_GET["p"], PASSWORD_DEFAULT);

if (mysqli_num_rows(mysqli_query($db, "SELECT email FROM admins WHERE email='$e'"))) die(respond("EMAIL_ALREADY_EXISTS"));

if (mysqli_query($db, "INSERT INTO `admins` (`email`,`password`,`name`,`phone`) VALUES ('$e','$p','Munish Kumar','9999999999')")) respond("SUCCESS"); 
else respond("UNKNOWN_ERROR");
?>
<?php ?>