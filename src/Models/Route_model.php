<?php
namespace Backend\Models;

use Backend\Database;
use Backend\Exceptions\RouteException;
use Zend\Config\Config;

class Route_model {
    private $db;
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
        $this->db = new Database(
            $config->database->host,
            $config->database->username,
            $config->database->password,
            $config->database->database
        );
    }

    public function saveRoute($id_user, $start_date, $route, $kms) {
        $id_user = (int) $id_user;
        $kms = (float) $kms;

        if (
            empty($id_user) ||
            empty($start_date) ||
            empty($route) ||
            empty($kms)
        ) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        if (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$/', $start_date)) {
            throw new RouteException("Start date format is not valid", RouteException::START_DATE_FORMAT_NOT_VALID);
        }

        try {
            json_decode($route);
        } catch (\Exception $e) {
            throw $e;
        }

        $stmt = $this->db->prepare("
            INSERT INTO
                routes (id_user, date, start_date, route, kms)
            VALUES
                (:id_user, :date, :start_date, '".$route."', :kms)
        ");

        $stmt->execute(array(
            ":id_user" => $id_user,
            ":date" => date('Y-m-d H:i:s'),
            ":start_date" => $start_date,
            //":route" => $route,
            ":kms" => $kms
        ));
    }
}