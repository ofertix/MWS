<?php

namespace Ofertix\Mws;

use Ofertix\Mws\Model\AmazonDeleteProduct;
use Ofertix\Mws\Model\AmazonFeedTypeInterface;
use Ofertix\Mws\Model\AmazonPrice;
use Ofertix\Mws\Model\AmazonProduct;
use Ofertix\Mws\Model\AmazonProductImage;
use Ofertix\Mws\Model\AmazonRequest;
use Ofertix\Mws\Model\AmazonStock;


class FeedClient
{
    const XSD_DIR = 'xsd';

    use Model\AmazonFeedTypeTrait;

    const FEED_TYPE_RELATIONSHIP = 'Relationship';
    const RELATIONSHIPS_FEED = '_POST_PRODUCT_RELATIONSHIP_DATA_';

    const OPERATION_TYPE_UPDATE = 'Update';
    const OPERATION_TYPE_DELETE = 'Delete';
    const OPERATION_TYPE_PARTIAL_UPDATE = 'PartialUpdate';

    private $config;
    private $client;
    private $requestClass;
    private $productClass;
    private $relationshipClass;
    private $orderFulfillmentClass;
    private $orderAcknowledgementClass;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = MwsClientFactory::getClient($config, 'feed');
        $this->requestClass = isset($config['amazon_request_class']) ?
            $config['amazon_request_class'] : '\Ofertix\Mws\Model\AmazonRequest';
        $this->productClass = isset($config['amazon_product_class']) ?
            $config['amazon_product_class'] : '\Ofertix\Mws\Model\AmazonProduct';
        $this->relationshipClass = isset($config['amazon_relationship_class']) ?
            $config['amazon_relationship_class'] : '\Ofertix\Mws\Model\AmazonRelationship';
        $this->imageClass = isset($config['amazon_image_class']) ?
            $config['amazon_image_class'] : '\Ofertix\Mws\Model\AmazonProductImage';
        $this->stockClass = isset($config['amazon_stock_class']) ?
            $config['amazon_stock_class'] : '\Ofertix\Mws\Model\AmazonStock';
        $this->priceClass = isset($config['amazon_price_class']) ?
            $config['amazon_price_class'] : '\Ofertix\Mws\Model\AmazonPrice';
        $this->deleteClass = isset($config['amazon_delete_class']) ?
            $config['amazon_delete_class'] : '\Ofertix\Mws\Model\AmazonDeleteProduct';
        $this->orderFulfillmentClass = isset($config['amazon_orderfulfillment_class']) ?
            $config['amazon_orderfulfillment_class'] : '\Ofertix\Mws\Model\AmazonOrderFulfillment';
        $this->orderAcknowledgementClass = isset($config['amazon_orderacknowledgement_class']) ?
            $config['amazon_orderacknowledgement_class'] : '\Ofertix\Mws\Model\AmazonOrderAcknowledgement';

    }
    /**
     * Get FeedSubmiisonList full info
     * @param array $status
     * @param array $feedTypes
     * @param int $limit
     * @return array
     */
    public function getSubmissionList($status = array(), $feedTypes = array() ,$limit = 20)
    {
        $request = new \MarketplaceWebService_Model_GetFeedSubmissionListRequest();
        $request->setMerchant($this->config['merchant_id']);

        $status = (count($status) > 0 ) ? $status : $this->getPendingStatusList();
        $statusList = new \MarketplaceWebService_Model_StatusList($status);
        $request->setFeedProcessingStatusList($statusList);

        $feedTypes = (count($feedTypes)>0 ) ? $feedTypes : $this->getFeedTypes();
        $feedTypeList = new \MarketplaceWebService_Model_TypeList();
        foreach ($feedTypes as $feedType) {
            $feedTypeList->withType($feedType);
        }
        $request->setFeedTypeList($feedTypeList);
        $request->setMaxCount($limit);

        $response = $this->client->getFeedSubmissionList($request);
        $submissions = array();
        if ($response->isSetGetFeedSubmissionListResult()) {
            $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
            $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
            foreach ($feedSubmissionInfoList as $feedSubmissionInfo) {
                $submissions[] = $this->getSubmission($feedSubmissionInfo->getFeedSubmissionId());
            }
            return $submissions;
        }


    }

    /** Get FeedSubmissionInfo
     * @param $feedSubmissionId
     * @return bool|\SimpleXMLElement
     */
    public function getSubmission($feedSubmissionId){

        if ($feedSubmissionId > 0) {
            $request = new \MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
            $request->setFeedSubmissionId($feedSubmissionId);
            $request->setMarketplace($this->config['marketplace_id']);
            $request->setMerchant($this->config['merchant_id']);
            $handle = @fopen('php://memory', 'rw+');
            $request->setFeedSubmissionResult($handle);
            $this->client->getFeedSubmissionResult($request);
            rewind($handle);
            $xmlResponse = stream_get_contents($handle);
            try {
                return new \SimpleXMLElement($xmlResponse);
            } catch(\Exception $ex) {
                return $xmlResponse;
            }
        } else {
            return false;
        }

    }


    /**
     * Get configuration array or value
     * @param null $value
     * @return array|bool
     */
    public function getConfig($value = null) {
        if (!empty($value)){
            return (isset($this->config[$value])) ? $this->config[$value]: false ;
        } else {
            return $this->config;
        }
    }

    /**
     * Set configuration value
     * @param $configKey
     * @param null $value
     * @return bool
     */
    public function setConfig($configKey, $value) {
        if (!empty($value) && isset($this->config[$configKey])) {
            $this->config[$configKey] = $value;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param AmazonProduct[] $amazonProducts For maximum performance count($amazonProducts) < 12000
     * @param string $marketPlaceId
     *
     * @return AmazonRequest
     * @throws \Exception
     */
    public function createProducts($amazonProducts, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonProducts as $amazonProduct) {
            if ($amazonProduct instanceof $this->productClass) {
                continue;
            }
            throw new \Exception('Products must be or extend \Ofertix\Mws\Model\AmazonProduct');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonProducts);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonProduct);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }

    public function createRelationships($amazonRelationships, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonRelationships as $relationship) {
            if (!($relationship instanceof $this->relationshipClass)) {
                throw new \Exception('Relationships must be or extend \Ofertix\Mws\Model\AmazonRelationship');
            }
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonRelationships);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $relationship);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }

    /**
     * @param AmazonProductImage[] $amazonProductImages For maximum performance count($amazonProducts) < 12000
     * @param string $marketPlaceId
     *
     * @return AmazonRequest
     * @throws \Exception
     */
    public function updateImages($amazonProductImages, $marketPlaceId = 'default')
    {

        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonProductImages as $amazonProductImage) {
            if ($amazonProductImage instanceof $this->imageClass) {
                continue;
            }
            throw new \Exception('ProductImage must be or extend \Ofertix\Mws\Model\AmazonProductImage');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonProductImages);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonProductImage);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


    /**
     * @param AmazonStock[] $amazonStocks For maximum performance count($amazonProducts) < 12000
     * @param string $marketPlaceId
     *
     * @return AmazonRequest
     * @throws \Exception
     */
    public function updateStock($amazonStocks, $marketPlaceId = 'default')
    {

        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonStocks as $amazonStock) {
            if ($amazonStock instanceof $this->stockClass) {
                continue;
            }
            throw new \Exception('Stock must be or extend \Ofertix\Mws\Model\AmazonStock');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonStocks);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonStock);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


    /**
     * @param AmazonPrice[] $amazonPrices For maximum performance count($amazonProducts) < 12000
     * @param string $marketPlaceId
     *
     * @return AmazonRequest
     * @throws \Exception
     */
    public function updatePrice($amazonPrices, $marketPlaceId = 'default')
    {

        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonPrices as $amazonPrice) {
            if ($amazonPrice instanceof $this->priceClass) {
                continue;
            }
            throw new \Exception('Price must be or extend \Ofertix\Mws\Model\AmazonPrice');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonPrices);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonPrice);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


    /**
     * @param AmazonDeleteProduct[] $amazonDeleteProducts For maximum performance count($amazonProducts) < 12000
     * @param string $marketPlaceId
     *
     * @return AmazonRequest
     * @throws \Exception
     */
    public function deleteProducts($amazonDeleteProducts, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        foreach ($amazonDeleteProducts as $amazonDeleteProduct) {
            if ($amazonDeleteProduct instanceof $this->deleteClass) {
                continue;
            }
            throw new \Exception('Products must be or extend \Ofertix\Mws\Model\AmazonDeleteProduct');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonDeleteProducts, false , self::OPERATION_TYPE_DELETE);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonDeleteProduct);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


    /**
     * @param AmazonFeedTypeInterface[] $feedTypeObjects
     * @param bool|false $clear
     * @param string $operationType
     *
     * @return \DOMDocument
     */
    private function createXmlFeed($feedTypeObjects, $clear = false , $operationType = self::OPERATION_TYPE_UPDATE)
    {
        $messageId=1;
        $feedName = $feedTypeObjects[0]->feedName();

        /** @var \DOMDocument $baseFeed */
        $baseFeed = $this->getMWSBaseFeed($feedName, $clear);

        /** @var AmazonFeedTypeInterface $feedTypeObject */
        foreach ($feedTypeObjects as $feedTypeObject) {
            try {
                /** @var \SimpleXMLElement $feed */
                $feed = $feedTypeObject->xmlNode();

                $validatedFeed = self::validateFeed($feed->asXML(), $feedName);
                if ($validatedFeed !== true) {
                    throw new \Exception(var_export($validatedFeed));
                }

            } catch (\Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                continue;
            }

            /** @var \DOMDocument $messageNodeXml */
            $messageNodeXml = $this->getMessageNode($messageId, $operationType);

            /** @var \DOMDocument $domMessageNode */
            $domMessageNode = $messageNodeXml->importNode(dom_import_simplexml($feed), true);

            $messageNodeXml->documentElement->appendChild($domMessageNode);
            $domMessageFeed = $baseFeed->importNode($messageNodeXml->documentElement, true);
            $baseFeed->documentElement->appendChild($domMessageFeed);
            $messageId++;
        }

        return $baseFeed;
    }

    /**
     * @param $feedType
     * @param bool|false $clearReplace
     *
     * @return \DOMDocument
     */
    private function getMWSBaseFeed($feedType, $clearReplace = false)
    {
        $mwsXmlHeader = <<<HERE_DOC
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" />
HERE_DOC;

        $feedXml = new \SimpleXMLElement($mwsXmlHeader);
        $header = $feedXml->addChild('Header');
        $header->addChild('DocumentVersion', '1.01');
        $header->addChild('MerchantIdentifier', $this->config['merchant_id']);
        $feedXml->addChild('MessageType', $feedType);
        if ($clearReplace) {
            $purgeString = ($clearReplace) ? 'true' : 'false';
            $feedXml->addChild('PurgeAndReplace', $purgeString);
        }

        return $this->simplexml2DomDoc($feedXml);
    }

    /**
     * @param \SimpleXMLElement $objXMl
     * @return \DOMDocument
     */
    private function simplexml2DomDoc(\SimpleXMLElement $objXMl)
    {
        $domXml = new \DOMDocument('1.0');
        $domXml->formatOutput = true;
        $domRootFeed = dom_import_simplexml($objXMl);
        $domRootFeed = $domXml->importNode($domRootFeed, true);
        $domRootFeed = $domXml->appendChild($domRootFeed);

        return $domXml;

    }

    /**
     * @param $feed
     * @param $feedName
     * @return bool|\LibXMLError
     * @throws \Exception
     */
    public function validateFeed($feed, $feedName)
    {
        $valid = false;
        $pathXSD = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.self::XSD_DIR.
            DIRECTORY_SEPARATOR.$feedName.'.xsd';
        if (file_exists($pathXSD)) {
            $xmlfeed = new \DOMDocument();
            $xmlfeed->resolveExternals = true;
            $xmlfeed->strictErrorChecking = true;
            $xmlfeed->preserveWhiteSpace = false;
            $xmlfeed->formatOutput = true;
            $xmlfeed->loadXML($feed);
            $valid = $xmlfeed->schemaValidate($pathXSD);
        } else {
            throw new \Exception("XSD file for feedtype $feedName not found in path $pathXSD");
        }

        return ($valid === false) ? libxml_get_last_error() : true ;
    }

    /**
     * @param int $messageId
     * @param string $operationType
     *
     * @return \DOMDocument
     */
    private function getMessageNode($messageId = 1, $operationType = self::OPERATION_TYPE_UPDATE)
    {
        $messageNodeXml = new \SimpleXMLElement('<Message/>');
        $messageNodeXml->addChild('MessageID', $messageId);
        $messageNodeXml->addChild('OperationType', $operationType);

        return self::simplexml2DomDoc($messageNodeXml);
    }

    /**
     * @param \DOMDocument $feed
     * @param string $marketPlaceId
     * @param AmazonFeedTypeInterface $feedType
     *
     * @return \MarketplaceWebService_Model_SubmitFeedResponse
     */
    private function submitFeed(\DOMDocument $feed, $marketPlaceId = 'default', AmazonFeedTypeInterface $feedType)
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        /** @var  $request  \MarketplaceWebService_Model_SubmitFeedRequest */
        $request = self::getSubmitFeedRequest($feed, $marketPlaceId, $feedType->feedType());
        $response = $this->client->submitFeed($request);
        return $response;

    }

    /**
     * @param \DOMDocument $feed
     * @param $marketPlaceId
     * @param $feedType
     *
     * @return \MarketplaceWebService_Model_SubmitFeedRequest
     */
    private function getSubmitFeedRequest(\DOMDocument $feed, $marketPlaceId, $feedType)
    {
        file_put_contents('/tmp/mws', $feed->saveXML());
        $feedHandle = fopen('/tmp/mws', 'r');
        rewind($feedHandle);
        $merchantID = $this->config['merchant_id'];

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
     * @param \MarketplaceWebService_Model_SubmitFeedResponse $response
     * @param \DOMDocument $feedObj
     *
     * @return AmazonRequest
     */
    private function getRequestData(\MarketplaceWebService_Model_SubmitFeedResponse $response, \DOMDocument $feedObj)
    {
        $responseMetadata = null;
        $feedSubmissionInfo = null;

        if ($response->isSetResponseMetadata()) {
            /** @var  $responseMetadata \MarketplaceWebService_Model_ResponseMetadata */
            $responseMetadata = $response->getResponseMetadata();
        }
        if ($response->isSetSubmitFeedResult()) {
            /** @var  $submitFeedResult \MarketplaceWebService_Model_SubmitFeedResult */
            $submitFeedResult = $response->getSubmitFeedResult();
            if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                /** @var  $feedSubmissionInfo \MarketplaceWebService_Model_FeedSubmissionInfo */
                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
            }
        }
        $headersMetadata = $response->getResponseHeaderMetadata();

        $amazonRequest = new $this->requestClass(
            $responseMetadata->getRequestId(),
            $feedSubmissionInfo->getFeedSubmissionId(),
            $feedSubmissionInfo->getFeedType(),
            $feedSubmissionInfo->getSubmittedDate(),
            $feedObj->saveXML(),
            AmazonRequest::REQUEST_TYPE_FEED,
            $feedSubmissionInfo->getFeedProcessingStatus(),
            $headersMetadata->getQuotaRemaining()
        );

        return $amazonRequest;
    }


    /**
     * @param \MarketplaceWebService_Model_SubmitFeedResponse $response
     */
    private function handleThrottling(\MarketplaceWebService_Model_SubmitFeedResponse $response)
    {
        /** @var  $headersMetadata  \MarketplaceWebService_Model_ResponseHeaderMetadata */
        $headersMetadata = $response->getResponseHeaderMetadata();
        //$quotaMax = $headersMetadata->getQuotaMax();
        $quotaRemaining = $headersMetadata->getQuotaRemaining();
        //$requestTimeStamp = $headersMetadata->getTimestamp();
        //$requestId = $headersMetadata->getRequestId();
        //$resetsAt = $headersMetadata->getQuotaResetsAt();
        while ($quotaRemaining < 1) {
            echo 'FeedClient: Has been reached the limit of requests. Waiting 5 minutes to continue... '.'QuotaRemaining: '.$quotaRemaining;
            sleep(5 * 60);
        }
    }

    /**
     * @param $amazonOrders
     * @param string $marketPlaceId
     * @return AmazonRequest
     * @throws \Exception
     */
    public function updateOrderFulfillment($amazonOrders, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;
        foreach ($amazonOrders as $amazonOrder) {
            if ($amazonOrder instanceof $this->orderFulfillmentClass) {
                continue;
            }
            throw new \Exception('OrderFulfillment must be or extend \Ofertix\Mws\Model\AmazonOrderFulfillment');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonOrders);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonOrder);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }

    /**
     * @param $amazonOrders
     * @param string $marketPlaceId
     * @return AmazonRequest
     * @throws \Exception
     */
    public function cancelOrder($amazonOrders, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;
        foreach ($amazonOrders as $amazonOrder) {
            if ($amazonOrder instanceof $this->orderAcknowledgementClass) {
                continue;
            }
            throw new \Exception('ProductImage must be or extend \Ofertix\Mws\Model\AmazonOrderFulfillment');
        }

        /** @var \DOMDocument $xmlFeed */
        $xmlFeed = $this->createXmlFeed($amazonOrders);

        /** @var  \MarketplaceWebService_Model_SubmitFeedResponse $response */
        $response = $this->submitFeed($xmlFeed, $marketPlaceId, $amazonOrder);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }

    /**
     * Get amazon report by id
     * @param $feedReportId
     * @return bool|string
     */
    public function getReport($feedReportId){

        if ($feedReportId > 0) {
            $request = new \MarketplaceWebService_Model_GetReportRequest();
            $request->setMerchant($this->config['merchant_id']);
            $request->setReportId($feedReportId);
            $handle = @fopen('php://memory', 'rw+');
            $request->setReport($handle);
            $this->client->getReport($request);
            rewind($handle);

            return stream_get_contents($handle);
        } else {
            return false;
        }

    }
}
