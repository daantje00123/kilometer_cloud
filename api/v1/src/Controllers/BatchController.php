<?php
namespace Backend\Controllers;

use Backend\Models\RouteModel;
use Interop\Container\ContainerInterface;

class BatchController extends Controller {
    private $routeModel;

    /**
     * BatchController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci) {
        parent::__construct($ci);

        $this->routeModel = new RouteModel($this->config);
    }

    /**
     * Change paid status in batch
     *
     * Required POST data:
     *  routes              Array with route id's
     *  status              Either 0 (not paid) or 1 (paid)
     *
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function changePaidStatus($req, $res) {
        $body = $req->getParsedBody();

        $routes = (isset($body['routes']) ? $body['routes'] : null);
        $status = (isset($body['status']) ? $body['status'] : null);
        $id_user = (int) $req->getAttribute('jwt')->data->id_user;

        if (empty($routes) || empty($id_user) || ($status != 0 && $status != 1)) {
            return $res->withJson(array(
                'success' => false,
                'message' => 'Data is not valid'
            ), 400);
        }

        foreach($routes as $id_route) {
            try {
                $route = $this->routeModel->getRouteById($id_route, $id_user);
                $this->routeModel->editRoute($id_route, $id_user, $route['omschrijving'], $status);
            } catch (\Exception $e) {
                return $res->withJson(array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ), 500);
            }
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Paid status changed'
        ));
    }

    /**
     * @param       \Psr\Http\Message\ServerRequestInterface        $req        The client request
     * @param       \Psr\Http\Message\ResponseInterface             $res        The server response
     *
     * @return      \Psr\Http\Message\ResponseInterface
     */
    public function deleteRoutes($req, $res) {
        $routes = (isset($req->getQueryParams()['routes']) ? json_decode($req->getQueryParams()['routes']) : null);
        $id_user = (int) $req->getAttribute('jwt')->data->id_user;

        if (empty($routes) || empty($id_user)) {
            return $res->withJson(array(
                'success' => false,
                'message' => 'Data is not valid'
            ), 400);
        }

        foreach($routes as $id_route) {
            try {
                $this->routeModel->deleteRoute($id_route, $id_user);
            } catch (\Exception $e) {
                return $res->withJson(array(
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ), 500);
            }
        }

        return $res->withJson(array(
            'success' => true,
            'message' => 'Routes deleted'
        ));
    }
}