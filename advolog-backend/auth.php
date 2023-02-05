<?php

//error_reporting(0);

function message($m)
{
    $r["message"] = $m;
    echo (json_encode($r));
}

function FindEmployee($db, $email)
{
    return (mysqli_num_rows(mysqli_query($db, "SELECT `email` FROM `employees` WHERE `email`='$email'")));
}

function FindAdmin($db, $email)
{
    return (mysqli_num_rows(mysqli_query($db, "SELECT `email` FROM `admins` WHERE `email`='$email'")));
}

if (!($_SERVER["REQUEST_METHOD"] == "POST")) die(message("INVALID_REQUEST"));

$db = mysqli_connect("localhost", "root", "", "advolog");
if (!$db) die(message("DATABASE_CONNECTION_ERROR"));

if (array_key_exists("AdminSignin", $_POST)) {
    //  Admin Signin
    $email = $_POST["AdminSigninEmail"];
    $password = $_POST["AdminSigninPassword"];
    if (!FindAdmin($db, $email)) die(message("NO_ACCOUNT_EXISTS"));
    $data = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `admins` WHERE `email`='$email'"));
    if (!password_verify($password, $data["password"])) die(message("INCORRECT_PASSWORD"));
    $data["message"] = "SUCCESS";
    echo (json_encode($data));
} else if (array_key_exists("EmployeeSignin", $_POST)) {
    //  Employee Signin
    $email = $_POST["EmployeeSigninEmail"];
    $password = $_POST["EmployeeSigninPassword"];
    if (!FindEmployee($db, $email)) die(message("NO_ACCOUNT_EXISTS"));
    $data = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `employees` WHERE `email`='$email'"));
    if (!password_verify($password, $data["password"])) die(message("INCORRECT_PASSWORD"));
    $data["message"]="SUCCESS";
    echo (json_encode($data));
} else if (array_key_exists("EmployeeSignup", $_POST)) {
    //  Employee Signup
    $email = $_POST["EmployeeSignupEmail"];
    if (FindEmployee($db, $email)) die(message("ALREADY_EXISTS"));
    $password = password_hash($_POST["EmployeeSignupPassword"], PASSWORD_DEFAULT);
    $name = $_POST["EmployeeSignupName"];
    $phone = $_POST["EmployeeSignupPhone"];
    if (mysqli_query($db, "INSERT INTO `employees` (`email`,`password`,`name`,`phone`) VALUES ('$email','$password','$name','$phone')")) {
        session_start();
        $_SESSION["EmployeeSignupEmail"] = $email;
        $_SESSION["code"] = 123456;
        // email
        message("SUCCESS");
    } else message("FAILED");
} else if (array_key_exists("EmployeeSignupVerification", $_POST)) {
    //  Employee Signup Verification
    session_start();
    $email = $_SESSION["EmployeeSignupEmail"];
    $code = $_SESSION["code"];
    $user_code = $_POST["EmployeeSignupCode"];
    if (!($code == $user_code)) die(message("INCORRECT_CODE"));
    if (mysqli_query($db, "UPDATE `employees` SET `account_status`= 'VERIFIED' WHERE `email`='$email'")) {
        session_destroy();
        message("SUCCESS");
    } else message("FAILED");
} else if (array_key_exists("EmployeeReset", $_POST)) {
    //  Employee Reset
    $email = $_POST["EmployeeResetEmail"];
    if (!FindEmployee($db, $email)) die(message("NO_ACCOUNT_EXISTS"));
    session_start();
    $_SESSION["EmployeeResetEmail"] = $email;
    $_SESSION["code"] = 123456;
    // Email
    message("SUCCESS");
} else if (array_key_exists("EmployeeResetVerification", $_POST)) {
    //  Employee Reset Verification
    session_start();
    $email = $_SESSION["EmployeeResetEmail"];
    $code = $_SESSION["code"];
    $user_code = $_POST["EmployeeResetCode"];
    if (!($code == $user_code)) die(message("INCORRECT_CODE"));
    $password = password_hash($_POST["EmployeeSignupPassword"], PASSWORD_DEFAULT);
    if (mysqli_query($db, "UPDATE `employees` SET `password`='$password' WHERE `email`='$email'")) {
        session_destroy();
        message("SUCCESS");
    } else message("FAILED");
} else if (array_key_exists("EmployeeApprove", $_POST)) {
    //  Employee Reset Verification
    $email = $_POST["EmployeeEmail"];
    if (mysqli_query($db, "UPDATE `employees` SET `account_status`='APPROVED' WHERE `email`='$email'")) message("SUCCESS");
    else message("FAILED");
} else {
    message("NO_KEY");
}

?>
<?php ?>