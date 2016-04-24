<?php
namespace Backend\Controllers;

use Interop\Container\ContainerInterface;
use Zend\Config\Config;

/**
 * Class Controller
 * @package Backend
 * @subpackage Controllers
 */
class Controller {
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $ci;

    /**
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * Controller constructor.
     * @param \Interop\Container\ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
        $this->config = new Config(require(__DIR__.'/../../api/v1/config/config.php'));
    }
}