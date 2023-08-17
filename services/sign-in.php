<?php
require_once __DIR__ . "/../includes/session.php";

if (isset($_POST["androidId"]) && isset($_POST["userId"])) {
    try {
        $device = new Device();
        $device->setAndroidID($_POST["androidId"]);
        $device->setUserID($_POST["userId"]);
        $device->setEnabled(1);
        if ($device->read()) {
            if (isset($_POST["sims"])) {
                //save-phone-numbers-to-easify-start
                //change-api-url-to-live-url (https://api.easify.live/api)
                $apiUrl = 'https://api.easify.iocod.com/api/add-ec-numbers';
                $data = array(
                    'device' => $device,
                    'sims' => json_decode($_POST["sims"]),
                    'user_id' => $_POST["userId"]
                );
                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                //save-phone-numbers-to-easify-end
                $device->saveSims(json_decode($_POST["sims"]));
            }
            $device->getUser()->setLastLogin(date('Y-m-d H:i:s'));
            $device->getUser()->setLastLoginIP($_SERVER["REMOTE_ADDR"]);
            $device->getUser()->save();
            if (isset($_POST["androidVersion"]) && isset($_POST["appVersion"])) {
                $device->setAndroidVersion($_POST["androidVersion"]);
                $device->setAppVersion($_POST["appVersion"]);
                $device->save();
            }
            $_SESSION["userID"] = $device->getUserID();
            $_SESSION["email"] = $device->getUser()->getEmail();
            $_SESSION["name"] = $device->getUser()->getName();
            $_SESSION["isAdmin"] = $device->getUser()->getIsAdmin();
            $_SESSION["timeZone"] = $device->getUser()->getTimeZone();
            session_commit();
            $response =
                [
                    "success" => true,
                    "data" => [
                        "sessionId" => get_cookie(APP_SESSION_NAME),
                        "device" => $device,
                    ],
                    "error" => null
                ];
            echo json_encode($response);
            die;
        } else {
            $errorCode = 401;
            $error = __("error_device_not_found");
        }
    } catch (Throwable $t) {
        $errorCode = 500;
        $error = $t->getMessage();
    }
    $response = ["success" => false, "data" => null, "error" => ["code" => $errorCode, "message" => $error]];
    echo json_encode($response);
}