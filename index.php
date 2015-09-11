<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require './RlcCrm/KazApi.php';

$callStr = "";
$return  = null;
$error   = null;

$client = 'kaz';
$secret = '&0jfWIYhH^Bv';
$url    = "http://mini-crm.www.dev/kaz";

try {
    $api    = new RlcCrm\KazApi($client, $secret, $url);
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
    switch ($action) {
        case 'getOrders':
            $callStr   = 'api->getOrdersFrom(0, 0)';
            $return    = $api->getOrdersFrom(0, 0);
            break;
        case 'setStatus':
            $now       = new DateTime();
            $recId     = 1;
            $date      = $now->format('Y-m-d H:i:s');
            $status    = "test01";
            $comment   = "comment";
            $callStr   = "api->setStatus($recId, $date, $status, $comment)";
            $return    = $api->setStatus($recId, $date, $status, $comment);
            break;
        case 'setStatusBatch':
            $now       = new DateTime();
            $date      = $now->format('Y-m-d H:i:s');
            $statusLog = array(
                array('recId' => 1, 'date' => $date, 'status' => 'test01', 'comment' => 'comment01'),
                array('recId' => 2, 'date' => $date, 'status' => 'test01', 'comment' => 'comment02'),
                array('recId' => 3, 'date' => $date, 'status' => 'test01', 'comment' => 'comment03'),
            );
            $callStr   = "api->setStatus(" . print_r($statusLog, true) . ")";
            $return    = $api->setStatusBatch($statusLog);
            break;
        default:
            break;
    }
} catch (Exception $ex) {
    $error = array(
        'code'    => $ex->getCode(),
        'message' => $ex->getMessage(),
        'file'    => $ex->getFile(),
        'line'    => $ex->getLine(),
    );
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Пример интеграции с CRM</title>
    </head>
    <body>
        <div>
            <a href="index.php?action=getOrders">Тест метода getOrdersFrom</a><br />
            <a href="index.php?action=setStatus">Тест метода setStatus</a><br />
            <a href="index.php?action=setStatusBatch">Тест метода setStatusBatch</a><br />
        </div>
        <div>
            <h3>Код вызова метода:</h3>
            <div><?php echo $callStr; ?></div>
            <h3>Результат:</h3>
            <pre><?php print_r($return); ?></pre>
            <?php if (isset($error)) : ?>
                <h3>Ошибка:</h3>>
                <pre><?php print_r($error); ?></pre>
            <?php endif; ?>
        </div>
    </body>
</html>