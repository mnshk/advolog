<?php

$r;
function r($s)
{
    $r["response"] = $s;
    echo (json_encode($r));
}

date_default_timezone_set('Asia/Calcutta');

if ($_SERVER["REQUEST_METHOD"] !== "POST") die(r("invalid_request"));

$db = mysqli_connect("localhost", "root", "", "advolog");
if (!$db) die(r("connection_error"));

if (array_key_exists("addTask", $_POST)) {
    $title = $_POST["taskTitle"];
    $customer_name = $_POST["taskCustomerName"];
    $customer_phone = $_POST["taskCustomerPhone"];
    $created_on = date('d/m/Y', time());
    $assigned_to = $_POST["taskAssignedTo"];
    $status = $_POST["taskStatus"];
    $payment = $_POST["taskPayment"];

    if (mysqli_query($db, "INSERT INTO tasks (title, customer_name, customer_phone, created_on, assigned_to, `status`, payment) VALUES ('$title','$customer_name','$customer_phone','$created_on','$assigned_to','$status','$payment')"))
        r("success");
    else r("error");
} else if (array_key_exists("allEmployees", $_POST)) {
    $q = mysqli_query($db, "SELECT * FROM `employees` WHERE 1");
    $c = mysqli_num_rows($q);
    for ($i = 0; $i < $c; $i++) {
        $r[$i] = mysqli_fetch_assoc($q);
    }
    echo json_encode($r);
} else if (array_key_exists("allTasks", $_POST)) {
    $qr = mysqli_query($db, "SELECT * FROM `tasks` WHERE 1");
    $c = mysqli_num_rows($qr);
    for ($i = 0; $i < $c; $i++) {
        $q = mysqli_fetch_assoc($qr);
        $em = $q["assigned_to"];
        $nm = mysqli_fetch_assoc(mysqli_query($db, "SELECT `name` FROM `employees` WHERE `email`='$em'"))["name"];
        $q["assigned_to_name"] = $nm;
        $r[$i] = $q;
    }
    echo json_encode($r);
} else if (array_key_exists("countTasks", $_POST)) {
    $r["pending"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Pending'"));
    $r["finished"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Finished'"));
    $r["processing"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Processing'"));
    echo (json_encode($r));
} else if (array_key_exists("editTask", $_POST)) {
    $uuid = $_POST["taskUuid"];
    $title = $_POST["taskTitle"];
    $customer_name = $_POST["taskCustomerName"];
    $customer_phone = $_POST["taskCustomerPhone"];
    $created_on = date('d/m/Y', time());
    $assigned_to = $_POST["taskAssignedTo"];
    $status = $_POST["taskStatus"];
    $payment = $_POST["taskPayment"];

    if (mysqli_query($db, "UPDATE `tasks` SET title = '$title', customer_name = '$customer_name', customer_phone = '$customer_phone', created_on = '$created_on', assigned_to = '$assigned_to', `status` = '$status', payment = '$payment' WHERE `uuid`='$uuid'"))
        r("success");
    else r("error");
} else if (array_key_exists("countTasksByEmployee", $_POST)) {
    $em = $_POST["email"];
    $r["pending"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Pending' AND `assigned_to`='$em'"));
    $r["finished"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Finished' AND `assigned_to`='$em'"));
    $r["processing"] = mysqli_num_rows(mysqli_query($db, "SELECT `uuid` FROM `tasks` WHERE `status`='Processing' AND `assigned_to`='$em'"));
    echo (json_encode($r));
}

?>
<?php ?>