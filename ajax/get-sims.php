<?php
/**
 * @var User $logged_in_user
 */

try {
    require_once __DIR__ . "/../includes/ajax_protect.php";
    require_once __DIR__ . "/../includes/login.php";

    $user = $logged_in_user;
    if (isset($_GET["user"]) && $_GET["user"] != $_SESSION["userID"] && $_SESSION["isAdmin"]) {
        $user = User::getById($_GET["user"]);
    }
    $simCards = $user->getUISims();
    $data = [];
    foreach ($simCards as $sim) {
        $row = [];
        $row[] = $sim['name'];
        $row[] = ($sim['deviceId']->model)  ? $sim['deviceId']->model : '';
        $row[] = (isset($sim)) ? "</i><button class='btn btn-danger btn-sm' onClick='deleteSim(".$sim['ID'].")' value=''><i class ='icon fa fa-remove'> Delete</button>" : '';
        $data[] = $row;
    }

    echo json_encode([
        "data" => $data
    ]);
} catch (Throwable $t) {
    echo json_encode(array(
        'error' => $t->getMessage()
    ));
}

