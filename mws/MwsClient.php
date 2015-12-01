<?php

require_once __DIR__.'/src/MarketplaceWebServiceProducts/Client.php';
require_once __DIR__.'/src/FBAInventoryServiceMWS/Client.php';
require_once __DIR__.'/src/MarketplaceWebServiceOrders/Client.php';
require_once __DIR__.'/src/MarketplaceWebService/Client.php';

class MwsClient
{
    const XSD_DIR = 'xsd';
    const FEED_AND_REPORT = 0;
    const PRODUCTS = 1;
    const ORDERS = 2;
    const FBA_INVENTORY = 3;

    private static $clients = array(
        self::FEED_AND_REPORT => 'feed',
        self::PRODUCTS => 'product',
        self::ORDERS => 'order',
        self::FBA_INVENTORY =>'inventory');

    /**
     * @param string $feed
     * @param string $feedType
     * @return bool
     */
    public static function validateFeed($feed, $feedType)
    {
        $valid = false;
        $pathXSD = __DIR__ . DIRECTORY_SEPARATOR. self::XSD_DIR. DIRECTORY_SEPARATOR . $feedType.'.xsd';
        if (file_exists($pathXSD)) {
            $xmlfeed = new \DOMDocument();
            $xmlfeed->resolveExternals = true;
            $xmlfeed->strictErrorChecking = true;
            $xmlfeed->preserveWhiteSpace = false;
            $xmlfeed->formatOutput = false;
            $xmlfeed->loadXML($feed);
            try {
                $valid = $xmlfeed->schemaValidate($pathXSD);
            } catch (\Exception $ex) {
                //throw new \Exception($ex->getMessage(), 1);
            }
        }

        return $valid;
    }


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
        } else {
            return false;
        }
    }

}

