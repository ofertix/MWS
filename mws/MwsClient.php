<?php


class MwsClient
{
    public $type;
    public function __construct($config,$type='feed'){
        $a = 2;
        switch($type) {
            case 'inventory':
                require_once __DIR__.'/src/FBAInventoryServiceMWS/Client.php';
                $this->client = new \FBAInventoryServiceMWS_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case 'orders':
                require_once __DIR__.'/src/MarketplaceWebServiceOrders/Client.php';
                $this->client = new \MarketplaceWebServiceOrders_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case 'product':
                require_once __DIR__.'/src/MarketplaceWebServiceProducts/Client.php';
                $this->client = new \MarketplaceWebServiceProducts_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;
            default:
                require_once __DIR__.'/src/MarketplaceWebService/Client.php';
                $this->client = new \MarketplaceWebService_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;
        }

    }
}

