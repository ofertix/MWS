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

    // ****** AMAZON MWS RELATED CONSTANTS  ********

    const PRODUCT_FEED = '_POST_PRODUCT_DATA_';
    const RELATIONSHIPS_FEED = '_POST_PRODUCT_RELATIONSHIP_DATA_';
    const SINGLE_FORMAT_ITEM_FEED = '_POST_ITEM_DATA_';
    const SHIPPING_OVERRIDE_FEED = '_POST_PRODUCT_OVERRIDES_DATA_';
    const PRODUCT_IMAGES_FEED = '_POST_PRODUCT_IMAGE_DATA_';
    const PRICING_FEED = '_POST_PRODUCT_PRICING_DATA_';
    const INVENTORY_FEED = '_POST_INVENTORY_AVAILABILITY_DATA_';
    const ORDER_ACKNOWLEDGEMENT_FEED = '_POST_ORDER_ACKNOWLEDGEMENT_DATA_';
    const ORDER_FULFILLMENT_FEED = '_POST_ORDER_FULFILLMENT_DATA_';

    const MESSAGE_TYPE_PRODUCT = 'Product';
    const MESSAGE_TYPE_INVENTORY = 'Inventory';
    const MESSAGE_TYPE_PRICING = 'Price';
    const MESSAGE_TYPE_RELATIONSHIP = 'Relationship';
    const MESSAGE_TYPE_PRODUCT_IMAGE = 'ProductImage';

    const SUBMITTED = '_SUBMITTED_';
    const IN_PROGRESS = '_IN_PROGRESS_';
    const CANCELLED = '_CANCELLED_';
    const DONE = '_DONE_';



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

