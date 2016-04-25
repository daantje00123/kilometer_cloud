<?php
namespace Backend\Models;

use Backend\Database;
use Backend\Exceptions\RouteException;
use Zend\Config\Config;
use Zend\Stdlib\ArrayObject;

/**
 * Class RouteModel
 * @package Backend
 * @subpackage Models
 */
class RouteModel {
    /**
     * @var \Backend\Database
     */
    private $db;

    /**
     * @var |Zend\Config\Config
     */
    private $config;

    /**
     * RouteModel constructor.
     * @param   \Zend\Config\Config         $config
     */
    public function __construct(Config $config) {
        $this->config = $config;
        $this->db = new Database(
            $config->database->host,
            $config->database->username,
            $config->database->password,
            $config->database->database
        );
    }

    /**
     * Save a route to the database
     *
     * @param   int         $id_user        User id
     * @param   string      $start_date     Start date
     * @param   string      $route          JSON Route array
     * @param   float       $kms            Total kilometers
     * @throws  \Backend\Exceptions\RouteException
     * @throws  \Exception
     */
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

    /**
     * Get a single route
     *
     * @param   int         $id_route       Route id
     * @param   int         $id_user        User id
     * @return  array
     * @throws  \Backend\Exceptions\RouteException
     */
    public function getRouteById($id_route, $id_user) {
        $id_route = (int) $id_route;
        $id_user = (int) $id_user;

        if (empty($id_route) || empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        $stmt = $this->db->prepare("
            SELECT
                a.id_route,
                a.id_user,
                a.omschrijving,
                a.date,
                a.start_date,
                a.route,
                a.kms,
                a.betaald,
                b.username,
                b.email,
                b.firstname,
                b.middlename,
                b.lastname
            FROM
                routes AS a
            INNER JOIN
                auth_users AS b ON a.id_user = b.id_user
            WHERE
                a.id_route = :id_route
            AND
                a.id_user = :id_user
        ");

        $stmt->execute(array(
            ":id_route" => $id_route,
            ":id_user" => $id_user
        ));

        return $this->prepare_row($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Edit a route
     *
     * @param   int         $id_route       Route id
     * @param   int         $id_user        User id
     * @param   string      $description    Description
     * @param   boolean     $paid           Is the route paid or not
     * @throws  \Backend\Exceptions\RouteException
     */
    public function editRoute($id_route, $id_user, $description, $paid) {
        $id_route = (int) $id_route;
        $id_user = (int) $id_user;
        $description = (string) $description;
        $paid = (bool) $paid;

        if (empty($id_route) || empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        if (empty($description)) {
            $description = "Geen omschrijving";
        }

        $route = $this->getRouteById($id_route, $id_user);
        
        if (empty($route)) {
            throw new RouteException("Forbidden resource for user", RouteException::USER_NOT_VALID);
        }

        $stmt = $this->db->prepare("
            UPDATE
                routes
            SET
                omschrijving = :description,
                betaald = :paid
            WHERE
                id_route = :id_route
        ");

        $stmt->execute(array(
            ":description" => $description,
            ":paid" => $paid,
            ":id_route" => $id_route
        ));
    }

    /**
     * Delete a route
     *
     * @param   int         $id_route       Route id
     * @param   int         $id_user        User id
     * @throws  \Backend\Exceptions\RouteException
     */
    public function deleteRoute($id_route, $id_user) {
        $id_route = (int) $id_route;
        $id_user = (int) $id_user;

        if (empty($id_route) || empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        $stmt = $this->db->prepare("
            DELETE FROM
                routes
            WHERE
                id_route = :id_route
            AND
                id_user = :id_user
        ");

        $stmt->execute(array(
            ":id_route" => $id_route,
            ":id_user" => $id_user
        ));
    }

    /**
     * Get all the routes by a specific user
     *
     * @param   int         $id_user        User id
     * @param   int         $page_number    Page number (used for pagination)
     * @param   int         $month          Filter month
     * @param   int         $year           Filter year
     * @param   int         $paid           Filter paid (0 = not paid, 1 = paid, 2 = all)
     * @return  array
     * @throws  RouteException
     * @throws  \Exception
     */
    public function getRoutesByUserId($id_user, $page_number, $month = 0, $year = 0, $paid = 2) {
        $id_user = (int) $id_user;
        $page_number = (int) $page_number;
        $month = (int) $month;
        $year = (int) $year;
        $paid = (int) $paid;

        if (empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        if ($month > 12) {
            $month = 0;
        }

        if ($year < 2016) {
            $year = 2016;
        }

        if ($paid < 0 || $paid > 2) {
            $paid = 2;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        $offset = ($page_number - 1) * 10;

        $sql = "
            SELECT
                a.id_route,
                a.id_user,
                a.omschrijving,
                a.date,
                a.start_date,
                a.route,
                a.kms,
                a.betaald,
                b.username,
                b.email,
                b.firstname,
                b.middlename,
                b.lastname
            FROM
                routes AS a
            INNER JOIN
                auth_users AS b ON a.id_user = b.id_user
            WHERE
                a.id_user = :id_user
            ";

        if ($month > 0) {
            $sql .= sprintf(" AND MONTH(a.start_date) = %d", $month);
        }

        $sql .= sprintf(" AND YEAR(a.start_date) = %d", $year);

        if ($paid != 2) {
            $sql .= " AND a.betaald = ".$paid;
        }

        $sql .= sprintf(" ORDER BY a.start_date DESC LIMIT 10 OFFSET %d", $offset);

        //var_dump($sql);exit;

        try {
            $this->db->getPDO()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $stmt = $this->db->prepare($sql);

            $stmt->execute(array(
                ":id_user" => $id_user
            ));
        } catch (\Exception $e) {
            throw $e;
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get total kilometers driven by a specific user and are not yet paid
     *
     * @param   int         $id_user        User id
     * @return  float
     * @throws  \Backend\Exceptions\RouteException
     */
    public function getTotalKmsByUserId($id_user) {
        $id_user = (int) $id_user;

        if (empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        $stmt = $this->db->prepare("
            SELECT
                SUM(kms) AS kms
            FROM
                routes
            WHERE
                id_user = :id_user
            AND
                betaald = 0
        ");

        $stmt->execute(array(
            ":id_user" => $id_user
        ));

        return (float) $stmt->fetch(\PDO::FETCH_OBJ)->kms;
    }

    /**
     * Get total price
     *
     * @param   int         $id_user        User id
     * @return  float
     * @throws  \Backend\Exceptions\RouteException
     */
    public function getTotalPriceByUserId($id_user) {
        $id_user = (int) $id_user;

        if (empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        return $this->getTotalKmsByUserId($id_user) * 0.15;
    }

    /**
     * Count all the routes
     *
     * @param   int         $id_user        User id
     * @param   int         $month          Filter month
     * @param   int         $year           Filter year
     * @param   int         $paid           Filter paid
     * @return  int
     * @throws  RouteException
     */
    public function getCountByUserId($id_user, $month = 0, $year = 0, $paid = 2) {
        $id_user = (int) $id_user;
        $month = (int) $month;
        $year = (int) $year;
        $paid = (int) $paid;

        if (empty($id_user)) {
            throw new RouteException("Not all data is valid", RouteException::DATA_NOT_VALID);
        }

        if ($month > 12) {
            $month = 0;
        }

        if ($year < 2016) {
            $year = 2016;
        }

        if ($paid < 0 || $paid > 2) {
            $paid = 2;
        }

        $sql = "
            SELECT
                COUNT(*) AS count
            FROM
                routes
            WHERE
                id_user = :id_user
        ";
        $params = array(
            ":id_user" => $id_user
        );

        if ($month > 0) {
            $sql .= " AND MONTH(start_date) = :month";
            $params[':month'] = $month;
        }

        $sql .= " AND YEAR(start_date) = :year";
        $params[':year'] = $year;

        if ($paid != 2) {
            $sql .= " AND betaald = :paid";
            $params[':paid'] = $paid;
        }

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return (int) $stmt->fetch(\PDO::FETCH_OBJ)->count;
    }

    /**
     * Parse all the database data with some more info
     *
     * @param   array       $row        Database row
     * @return  array
     */
    private function prepare_row($row) {
        $datetime = explode(' ', $row['date']);
        $date = explode('-', $datetime[0]);
        $time = explode(':', $datetime[1]);
        $row['datum']['eind'] = array(
            'day' => $date[2],
            'month' => $date[1],
            'year' => $date[0]
        );

        $row['tijd']['eind'] = array(
            'hours' => $time[0],
            'minutes' => $time[1],
            'seconds' => $time[2]
        );

        $datetime = explode(' ', $row['start_date']);
        $date = explode('-', $datetime[0]);
        $time = explode(':', $datetime[1]);
        $row['datum']['start'] = array(
            'day' => $date[2],
            'month' => $date[1],
            'year' => $date[0]
        );

        $row['tijd']['start'] = array(
            'hours' => $time[0],
            'minutes' => $time[1],
            'seconds' => $time[2]
        );

        $reis = (strtotime($row['date']) - strtotime($row['start_date']));

        $row['tijd']['reis'] = $this->sec2hms($reis);
        $row['gemiddelde'] = $row['kms']/($reis/60/60);

        if (!empty($row['route'])) {
            $row['route'] = json_decode($row['route']);
        }

        return $row;
    }

    /**
     * Convert seconds to hours
     *
     * @param   int         $sec        Seconds
     * @param   bool        $padHours   Add leading zero's to the numbers
     * @return  string
     */
    private function sec2hms($sec, $padHours = false)
    {

        // start with a blank string
        $hms = "";

        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $hours = intval(intval($sec) / 3600);

        // add hours to $hms (with a leading 0 if asked for)
        $hms .= ($padHours)
            ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
            : $hours. ":";

        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60);

        // add minutes to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

        // seconds past the minute are found by dividing the total number of seconds
        // by 60 and using the remainder
        $seconds = intval($sec % 60);

        // add seconds to $hms (with a leading 0 if needed)
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        // done!
        return $hms;

    }
}