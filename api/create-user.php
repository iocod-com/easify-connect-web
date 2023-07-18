<?php
require_once '../controller/Entity.php';
require_once '../model/User.php';
require_once '../config.php';
require_once '../includes/functions.php';

try {
    if (!empty($_POST["name"]) && !empty($_POST["email"])) {
        $user = new User();
        $user->setName($_POST["name"]);
        $user->setEmail($_POST["email"]);
        $user->setIsAdmin(false);
        $random_password = random_str(8);
        $user->setPassword($random_password);
        $user->setApiKey(generateAPIKey());
        $user->setDateAdded(date('Y-m-d H:i:s'));
        MysqliDb::getInstance()->startTransaction();
        $user->save();
        MysqliDb::getInstance()->commit();
        $response = array(
            "status" => true,
            "message" => "User created successfully",
            "data" => array(
                "email" => $_POST["email"],
                "password" => $random_password
            )
        );
        header('Content-Type: application/json');
        $jsonResponse = json_encode($response);
        echo $jsonResponse;
    }
} catch (Throwable $t) {
    header('Content-Type: application/json');
    echo json_encode(array(
        "status" => false,
        "message" => $t->getMessage(),
        "data" => []
    ));
}
