<?php
namespace Backend\Controllers;

use Backend\Models\RouteModel;
use Interop\Container\ContainerInterface;
use Zend\Config\Config;
use Exception;

class RouteController {
    private $model;
    private $config;
    private $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
        $this->config = new Config(require(__DIR__.'/../../api/v1/config/config.php'));
        $this->model = new RouteModel($this->config);
    }

    public function saveRoute($req, $res) {
        $body = $req->getParsedBody();

        $id_user = (!isset($body['id_user']) ? null : $body['id_user']);
        $start_date = (!isset($body['start_date']) ? null : $body['start_date']);
        $route = (!isset($body['route']) ? null : $body['route']);
        $kms = (!isset($body['kms']) ? null : $body['kms']);

        try {
            $this->model->saveRoute($id_user, $start_date, $route, $kms);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => "Route saved"
        ));
    }

    public function routeHistory($req, $res) {
        $id_user = $req->getAttribute('jwt')->data->id_user;
        $page_number = (isset($_GET['page']) ? $_GET['page'] : 1);

        try {
            $routes = $this->model->getRoutesByUserId($id_user, $page_number);
            $kms = $this->model->getTotalKmsByUserId($id_user);
            $price = $this->model->getTotalPriceByUserId($id_user);
            $count = $this->model->getCountByUserId($id_user);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Routes found',
            'routes' => $routes,
            'totals' => array(
                'kms' => $kms,
                'price' => $price,
                'count' => $count
            )
        ));
    }

    public function getSingleRoute($req, $res) {
        $id_route = (isset($req->getQueryParams()['id_route']) ? $req->getQueryParams()['id_route'] : 0);
        $id_user = $req->getAttribute('jwt')->data->id_user;

        try {
            $route = $this->model->getRouteById($id_route, $id_user);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ));
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route found',
            'route' => $route
        ));
    }

    public function editRoute($req, $res) {
        $body = $req->getParsedBody();

        $id_user = $req->getAttribute('jwt')->data->id_user;
        $id_route = (isset($body['id_route']) ? $body['id_route'] : null);
        $description = (isset($body['description']) ? $body['description'] : null);
        $paid = (isset($body['paid']) ? $body['paid'] : null);

        try {
            $this->model->editRoute($id_route, $id_user, $description, $paid);
            $route = $this->model->getRouteById($id_route, $id_user);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route changed',
            'route' => $route
        ));
    }

    public function deleteRoute($req, $res) {
        $id_route = (isset($req->getQueryParams()['id_route']) ? $req->getQueryParams()['id_route'] : 0);
        $id_user = $req->getAttribute('jwt')->data->id_user;

        try {
            $this->model->deleteRoute($id_route, $id_user);
        } catch (Exception $e) {
            return $res->withJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Route deleted'
        ));
    }
}