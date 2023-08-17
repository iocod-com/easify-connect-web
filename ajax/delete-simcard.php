<?php
require_once '../controller/Entity.php';
require_once '../model/User.php';
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../model/Device.php';
require_once '../model/DeviceUser.php';
require_once '../model/Sim.php';

try {
    if (isset($_POST["id"])) {
        $simID = $_POST["id"];
        MysqliDb::getInstance()->startTransaction();
        $sim = new Sim();
        $sim->setID($simID);
        if ($sim->read()) {
            $deviceID = $sim->getDeviceID();
            //delete-number-from-easify-start
            //change-api-url-to-live-url (https://api.easify.live/api)
            $apiUrl = 'https://api.easify.iocod.com/api/delete-ec-number';

            $data = array(
                'device_id' => $deviceID,
                'slot' => $sim->getSlot()
            );
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $sim->delete();
                $simCards = Sim::where("Sim.enabled", true)->where("deviceID", $deviceID)
                    ->read_all();
                if (count($simCards) <= 0) {
                    $device = new Device();
                    $device->setID($deviceID);
                    if ($device->read()) {
                        $device->delete();
                    }
                };

                MysqliDb::getInstance()->commit();
                $success = 'Sim card deleted successfully';
                echo json_encode(array(
                    'result' => $success
                ));
                exit;
            }
        }
        MysqliDb::getInstance()->commit();
        $message = 'Failed to delete sim';
        echo json_encode(array(
            'result' => $message
        ));

        curl_close($ch);
        //delete-number-from-easify-end
    }
} catch (Throwable $t) {
    echo json_encode(array(
        'error' => $t->getMessage()
    ));
}
