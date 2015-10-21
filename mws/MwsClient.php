<?php

require_once __DIR__.'/src/MarketplaceWebServiceProducts/Client.php';
require_once __DIR__.'/src/FBAInventoryServiceMWS/Client.php';
require_once __DIR__.'/src/MarketplaceWebServiceOrders/Client.php';
require_once __DIR__.'/src/MarketplaceWebService/Client.php';
class MwsClient
{
    public $type;
    public function __construct($config,$type='feed'){
        $type = 'product';
        switch($type) {
            case 'inventory':

                $client = new \FBAInventoryServiceMWS_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case 'order':

                $client = new \MarketplaceWebServiceOrders_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case 'product':

                $client = new \MarketplaceWebServiceProducts_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    $config['config']
                );
                break;
            default:

                $client = new \MarketplaceWebService_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['config'],
                    $config['app_name'],
                    $config['app_version']
                );
                break;
        }
        if (is_object($client)){
            $this->client = $client;
        }

    }
}


