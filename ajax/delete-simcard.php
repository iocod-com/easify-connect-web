<?php
require_once '../controller/Entity.php';
require_once '../model/User.php';
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../model/Device.php';
require_once '../model/DeviceUser.php';
require_once '../model/Sim.php';

try{
        if (isset($_POST["id"])) {
                $simID = $_POST["id"];
                MysqliDb::getInstance()->startTransaction();
                $sim = new Sim();
                $sim->setID($simID);
                if ($sim->read()) {
                    $deviceID = $sim->getDeviceID();
                    $sim->delete();
                    $simCards = Sim::where("Sim.enabled", true)->where("deviceID", $deviceID)
                    ->read_all();
                    if(count($simCards) <= 0){
                        $device = new Device();
                        $device->setID($deviceID);
                        if ($device->read()) {
                            $device->delete();
                        }
                    };
                }
                MysqliDb::getInstance()->commit();
                $success = 'Sim card deleted successfully';
                echo json_encode(array(
                    'result' => $success
                ));
        }
} catch (Throwable $t) {
    echo json_encode(array(
        'error' => $t->getMessage()
    ));
}