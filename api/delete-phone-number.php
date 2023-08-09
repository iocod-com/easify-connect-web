<?php
require_once '../controller/Entity.php';
require_once '../model/User.php';
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../model/Device.php';
require_once '../model/DeviceUser.php';
require_once '../model/Sim.php';


try {
    if (isset($_POST["device_id"]) && isset($_POST["slot"])) {
        $deviceID = $_POST["device_id"];
        $slot = $_POST["slot"];
        MysqliDb::getInstance()->startTransaction();
        $sim = new Sim();
        $sim->setDeviceID($deviceID);
        $sim->setSlot($slot);
        if ($sim->read()) {
            $sim->delete();
        }
        $simCards = Sim::where("Sim.enabled", true)->where("deviceID", $deviceID)
        ->read_all();
        if(count($simCards) <= 0){
            $device = new Device();
            $device->setID($deviceID);
            if ($device->read()) {
                $device->delete();
            }
        };
        MysqliDb::getInstance()->commit();
        $response = array(
            "status" => true,
            "message" => "Phone number deleted successfully",
            "data" => array(
            )
        );
        header('Content-Type: application/json');
        $jsonResponse = json_encode($response);
        echo $jsonResponse;
    }else{
        $response = array(
            "status" => false,
            "message" => "Phone number not found!",
            "data" => array(
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