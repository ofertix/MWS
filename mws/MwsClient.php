<?php

require_once __DIR__.'/src/MarketplaceWebServiceProducts/Client.php';
require_once __DIR__.'/src/FBAInventoryServiceMWS/Client.php';
require_once __DIR__.'/src/MarketplaceWebServiceOrders/Client.php';
require_once __DIR__.'/src/MarketplaceWebService/Client.php';
class MwsClient
{
    const FEED_AND_REPORT = 0;
    const PRODUCTS = 1;
    const ORDERS = 2;
    const FBA_INVENTORY = 3;

    private static $clients = array(
        self::FEED_AND_REPORT => 'feed',
        self::PRODUCTS => 'product',
        self::ORDERS => 'order',
        self::FBA_INVENTORY =>'inventory');

    public static function getClient($config,$type='feed'){

        switch($type) {
            case  self::FBA_INVENTORY:
                $client = new \FBAInventoryServiceMWS_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/FulfillmentInventory/2010-10-01"),
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case self::ORDERS:
                $client = new \MarketplaceWebServiceOrders_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Orders/2013-09-01")
                );

                break;

            case  self::PRODUCTS:
                $client = new \MarketplaceWebServiceProducts_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Products/2011-10-01")
                );
                break;
            case self::FEED_AND_REPORT:
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





