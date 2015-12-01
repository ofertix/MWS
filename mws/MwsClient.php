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

    const OPERATION_TYPE_UPDATE = 'Update';

    private static $mwsXmlHeader = <<<HERE_DOC
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" />
HERE_DOC;

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

    /**
     * @param string     $merchantIdentifier
     * @param string     $messageType
     * @param bool|false $clearReplace
     * @return SimpleXMLElement
     */
    public static function getMWSBaseFeed($merchantIdentifier, $messageType, $clearReplace = false)
    {
        $feedXml = new \SimpleXMLElement(self::$mwsXmlHeader);
        $header = $feedXml->addChild('Header');
        $header->addChild('DocumentVersion', '1.01');
        $header->addChild('MerchantIdentifier', $merchantIdentifier);
        $feedXml->addChild('MessageType', $messageType);
        if ($clearReplace) {
            $purgeString = ($clearReplace) ? 'true' : 'false';
            $feedXml->addChild('PurgeAndReplace', $purgeString);
        }

        return self::simplexml2DomDoc($feedXml);

    }


    /**
     * @param int    $messageId
     * @param string $operationType
     * @return SimpleXMLElement
     */
    public static function getMessageNode($messageId = 1, $operationType = self::OPERATION_TYPE_UPDATE)
    {
        $messageNodeXml = new \SimpleXMLElement('<Message/>');
        $messageNodeXml->addChild('MessageID', $messageId);
        $messageNodeXml->addChild('OperationType', $operationType);

        return self::simplexml2DomDoc($messageNodeXml);
    }


    /**
     * @param DOMDocument $feed
     * @param string      $marketPlaceId
     * @return MarketplaceWebService_Model_SubmitFeedRequest
     */
    public static function getSubmitFeedRequest(\DOMDocument $feed, $marketPlaceId)
    {
        file_put_contents('/tmp/mws', $feed->saveXML());
        $feedHandle = fopen('/tmp/mws', 'r');
        rewind($feedHandle);
        $messageType = $feed->getElementsByTagName('MessageType')->item(0)->nodeValue;
        $merchantID = $feed->getElementsByTagName('MerchantIdentifier')->item(0)->nodeValue;
        $feedType = '';
        switch ($messageType) {
            case self::MESSAGE_TYPE_PRODUCT:
                $feedType = self::PRODUCT_FEED;
                break;
            case self::MESSAGE_TYPE_PRICING:
                $feedType = self::PRICING_FEED;
                break;
            case self::MESSAGE_TYPE_INVENTORY:
                $feedType = self::INVENTORY_FEED;
                break;
            case self::MESSAGE_TYPE_PRODUCT_IMAGE:
                $feedType = self::PRODUCT_IMAGES_FEED;
                break;
            case self::MESSAGE_TYPE_RELATIONSHIP:
                $feedType = self::RELATIONSHIPS_FEED;
                break;
        }

        $request = new \MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($merchantID);
        $request->setMarketplaceIdList(array('Id' => $marketPlaceId));
        $request->setFeedType($feedType);
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        $request->setPurgeAndReplace(false);
        rewind($feedHandle);
        $request->setFeedContent($feedHandle);

        return $request;
    }

    /**
     * @param DOMDocument $feed
     * @param strting     $marketPlaceId
     * @param \MarketplaceWebService_Interface $client
     * @return bool|DOMDocument
     */
    public static function submitFeed(\DOMDocument $feed, $marketPlaceId, $client )
    {
        $request = self::getSubmitFeedRequest($feed, $marketPlaceId);
        $response = $client->submitFeed($request);

        if ($response->isSetSubmitFeedResult()) {
            $submitFeedResult = $response->getSubmitFeedResult();
            if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                $submInfo = $submitFeedResult->getFeedSubmissionInfo();

                return $submInfo;
            }
        } else {
            return $response;
        }

        return false;
    }

    /**
     * @param SimpleXMLElement $objXMl
     * @return DOMElement|DOMNode
     */
    public static function simplexml2DomDoc(\SimpleXMLElement $objXMl)
    {
        $domXml = new \DOMDocument('1.0');
        $domRootFeed = dom_import_simplexml($objXMl);
        $domRootFeed = $domXml->importNode($domRootFeed, true);
        $domRootFeed = $domXml->appendChild($domRootFeed);

        return $domXml;

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

