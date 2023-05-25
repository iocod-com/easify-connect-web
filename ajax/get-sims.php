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
    print_r($simCards);
    $data = [];
    foreach ($simCards as $sim) {
        $row[] = $sim;
        // if ($sim->isActive()) {
        //     $device = $sim->getSims();
        //     if ($_SESSION["isAdmin"] || $device->getUserID() == $logged_in_user->getID() || $device->getEnabled()) {
        //         $row = [];
        //         $disabled =  $device->getUserID() == $logged_in_user->getID() ? "" : "disabled";
        //         $row[] = "<label><input type='checkbox' name='devices[]' class='remove-devices' onchange='toggleRemove()' value='{$device->getID()}' {$disabled}></label>";
        //         $row[] = $device->getID();
        //         $name = is_null($sim->getName()) ? 'null' : "'" . htmlentities($sim->getName(), ENT_QUOTES) . "'";
        //         if ($user->getID() == $logged_in_user->getID()) {
        //             $row[] = "<a href=\"#devices\" onclick=\"editDevice({$name}, {$device->getID()})\">" . htmlentities(strval($sim), ENT_QUOTES) . "&nbsp;<i class=\"fa fa-edit\"></i></a>";
        //         } else {
        //             $row[] = htmlentities(strval($sim), ENT_QUOTES);
        //         }
        //         $row[] = htmlentities($device->getModel(), ENT_QUOTES);
        //         $row[] = $device->getAndroidVersion() != null ? $device->getAndroidVersion() : __("null_value");
        //         $row[] = $device->getAppVersion() != null ? $device->getAppVersion() : __("null_value");
        //         $row[] = Message::where("Message.deviceID", $device->getID())
        //             ->where("Message.userID", $user->getID())
        //             ->count();
        //         $row[] = $sim->isActive() && $device->getEnabled() ? '<label class="label label-success">' . __("connected") . '</label>' : '<label class="label label-danger">' . __("disconnected") . '</label>';
        //         if ($_SESSION["isAdmin"]) {
        //             if ($device->getUserID() == $logged_in_user->getID()) {
        //                 $sharedWith = $device->sharedToAll ? [] : $device->getUsers();
        //                 if ($device->sharedToAll) {
        //                     $sharedWithCount = $device->sharedToAll == 1 ? __('all_users') : __('demo_users');
        //                 } else {
        //                     $count = count($sharedWith) - 1;
        //                     if ($count > 0) {
        //                         $sharedWithCount = $count > 1 ? $count . " " .__('users') : $count . " " . __('user');
        //                     } else {
        //                         $sharedWithCount = __('no_one');
        //                     }
        //                 }
        //                 $sharedWith = json_encode($sharedWith);
        //                 $row[] = $sharedWithCount . "<a href=\"#devices\" style=\"margin-left: 5px\" onclick=\"shareDevice({$sharedWith}, {$device->getID()}, {$device->getSharedToAll()}, {$device->getUseOwnerSettings()})\"><i class=\"fa fa-share\"></i></a>";
        //             } else {
        //                 $row[] = __('unshareable');
        //             }
        //         }
        //         $data[] = $row;
        //     }
        // }
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
