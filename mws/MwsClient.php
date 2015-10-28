<?php

require_once __DIR__.'/src/MarketplaceWebServiceProducts/Client.php';
require_once __DIR__.'/src/FBAInventoryServiceMWS/Client.php';
require_once __DIR__.'/src/MarketplaceWebServiceOrders/Client.php';
require_once __DIR__.'/src/MarketplaceWebService/Client.php';
class MwsClient
{
    public static function getClient($config,$type='feed'){
        switch($type) {
            case 'inventory':
                $client = new \FBAInventoryServiceMWS_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/FulfillmentInventory/2010-10-01"),
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case 'order':
                $client = new \MarketplaceWebServiceOrders_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Orders/2013-09-01")
                );

                break;

            case 'product':
                $client = new \MarketplaceWebServiceProducts_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Products/2011-10-01")
                );
                break;
            default:
                $client = new \MarketplaceWebService_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    array('ServiceURL' => "https://mws.amazonservices.es"),
                    $config['app_name'],
                    $config['app_version']
                );
                break;
        }
        if (is_object($client)){
            return $client;
        }
    }

}





