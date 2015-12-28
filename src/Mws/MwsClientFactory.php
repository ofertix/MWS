<?php

namespace Ofertix\Mws;

class MwsClientFactory
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

    private static function isValidClient($type)
    {
        if (!in_array($type, self::$clients) ) {
            return false;
        }

        return true;
    }

    public static function getClient($config,$type='feed'){

        if (!self::isValidClient($type)) {
            throw new \Exception("Amazon Client does not exist , valid clients are ". implode(",",self::$clients));
        }
        switch($type) {
            case  self::$clients[self::FBA_INVENTORY]:
                $client = new \FBAInventoryServiceMWS_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/FulfillmentInventory/2010-10-01"),
                    $config['app_name'],
                    $config['app_version']
                );
                break;

            case  self::$clients[self::ORDERS]:
                $client = new \MarketplaceWebServiceOrders_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Orders/2013-09-01")
                );

                break;

            case  self::$clients[self::PRODUCTS]:
                $client = new \MarketplaceWebServiceProducts_Client(
                    $config['aws_access_id'],
                    $config['aws_access_secret'],
                    $config['app_name'],
                    $config['app_version'],
                    array('ServiceURL' => "https://mws-eu.amazonservices.com/Products/2011-10-01")
                );
                break;
            case  self::$clients[self::FEED_AND_REPORT]:
            default:
                $client = new \FeedClient(
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
        } else {
            return false;
        }
    }

}

