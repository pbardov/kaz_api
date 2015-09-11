<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RlcCrm;

spl_autoload_register(array('\\RlcCrm\\KazApi', 'autoLoad'));

/**
 * Description of KazApi
 *
 * @author pavel
 */
class KazApi
{

    private $_authClient = null;
    private $_authSecret = null;
    private $_apiUrl     = 'http://crm.rus-land.su/api2';

    public function __construct($authClient, $authSecret, $apiUrl = null)
    {
        $this->_apiUrl     = isset($apiUrl) ? $apiUrl : $this->_apiUrl;
        $this->_authClient = $authClient;
        $this->_authSecret = $authSecret;
    }

    /**
     * Получить заказы
     * если параметр type = 0, то в параметре from - recId последнего заказа, метод вернет все заказы с recId большим, чем переданный в from
     * если параметр type = 1, то в параметре from - recDate последнего заказа, метод вернет все заказы с recDate большей чем в from
     *
     * @param integer $type если 0 - параметр from это recId, 1 - параметр from - дата в формате Y-m-d H:i:s
     * @param mixed $from
     * @return \stdClass
     */
    public function getOrdersFrom($type, $from)
    {
        return $this->_request('getOrdersFrom', array($type, $from));
    }

    /**
     * Установить статус доставки/обработки
     *
     * @param integer $recId recId заказа
     * @param string $date строка даты формата Y-m-d H:i:s
     * @param string $status новый статус
     * @param string $comment комментарий к статусу
     * @return integer если > 0 то запись добавлена
     */
    public function setStatus($recId, $date, $status, $comment = '')
    {
        return $this->_request('setStatus', array($recId, $date, $status, $comment));
    }

    /**
     * Установить статус пакетно
     *
     * @param array $statusLog массив содержащий записи array('recId' => 1, 'date' => 'Y-m-d H:i:s', 'status' => 'status', 'comment' => 'comment')
     * @return integer количество добавленных записей
     */
    public function setStatusBatch(array $statusLog)
    {
        return $this->_request('setStatusBatch', array($statusLog));
    }

    protected function _request($method, array $params, $id = NULL)
    {
        $id    = isset($id) ? $id : (time() * 1000 + rand(0, 999));
        $data  = array(
            'method' => $method,
            'params' => json_encode($params),
            'id'     => $id,
        );
        $query = http_build_query($data);
        $sign  = $this->_getSign($query);

        $ch = curl_init($this->_apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Auth-Client: {$this->_authClient}",
            "Auth-Sign: {$sign}",
        ));

        $response = curl_exec($ch);
        if ($response === FALSE)
            throw new NetworkException();

        $respData = json_decode($response);
        if (!isset($respData)) {
            throw new FormatException(null, $respData);
        }
        if (!empty($respData->error)) {
            throw new ExecutionException(null, $respData->error);
        }

        return $respData->result;
    }

    protected function _getSign($query)
    {
        $queryMD5 = md5($query);
        return md5($this->_authClient . $this->_authSecret . $queryMD5);
    }

    public static function autoLoad($class, $extensions = null)
    {
        $myPath     = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        $extensions = isset($extensions) ? $extensions : spl_autoload_extensions();
        $extensions = explode(',', $extensions);
        $parts      = explode('\\', $class);
        $namespace  = count($parts) > 1 ? array_shift($parts) : '';
        $filePath   = implode(DIRECTORY_SEPARATOR, $parts);
        $fullPath   = $myPath . $filePath;
        if ($namespace === 'RlcCrm') {
            foreach ($extensions as $ext) {
                $ext = trim($ext);
                if (file_exists($fullPath . $ext)) {
                    require $fullPath . $ext;
                    return true;
                }
            }
            return false;
        }
        return false;
    }

}
