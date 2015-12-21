<?php

namespace Ofertix\Mws;

use Ofertix\Mws\Model\AmazonFeedTypeInterface;
use Ofertix\Mws\Model\AmazonProduct;
use Ofertix\Mws\Model\AmazonRequest;

class FeedClient
{
    const XSD_DIR = 'xsd';
    const FEED_TYPE_PRODUCT = 'Product';
    const FEED_TYPE_INVENTORY = 'Inventory';
    const FEED_TYPE_PRICING = 'Price';
    const FEED_TYPE_RELATIONSHIP = 'Relationship';
    const FEED_TYPE_PRODUCT_IMAGE = 'ProductImage';

    const PRODUCT_FEED = '_POST_PRODUCT_DATA_';
    const INVENTORY_FEED = '_POST_INVENTORY_AVAILABILITY_DATA_';
    const PRICING_FEED = '_POST_PRODUCT_PRICING_DATA_';
    const RELATIONSHIPS_FEED = '_POST_PRODUCT_RELATIONSHIP_DATA_';
    const PRODUCT_IMAGES_FEED = '_POST_PRODUCT_IMAGE_DATA_';

    const OPERATION_TYPE_UPDATE = 'Update';
    const OPERATION_TYPE_DELETE = 'Delete';
    const OPERATION_TYPE_PARTIAL_UPDATE = 'PartialUpdate';

    private $config;
    private $client;
    private $requestClass;
    private $productClass;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = MwsClient::getClient($config, 'feed');
        $this->requestClass = isset($config['amazon_request_class']) ? $config['amazon_request_class'] :'\Ofertix\Mws\Model\AmazonRequest';
        $this->productClass = isset($config['amazon_product_class']) ? $config['amazon_product_class'] :'\Ofertix\Mws\Model\AmazonProduct';
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
        $response = $this->submitFeed($xmlFeed, $marketPlaceId);

        $this->handleThrottling($response);
        /** @var AmazonRequest $amazonRequest */
        $amazonRequest = $this->getRequestData($response, $xmlFeed);

        return $amazonRequest;
    }


//        public function updateProductImages($amazonProductImages, $marketPlaceId = 'default')
//        {
//
//            $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;
//
//            foreach ($amazonProductImages as $amazonProductImage) {
//                if ($amazonProductImage instanceof $this->productClass) {
//                    continue;
//                }
//                throw new \Exception('ProductImage must be or extend \Ofertix\Mws\Model\AmazonProductImage');
//            }
//
//            /** @var \DOMDocument $xmlFeed */
//            $xmlFeed = $this->createXmlFeed($amazonProductImages,self::FEED_TYPE_PRODUCT_IMAGE);
//
//
//
//            foreach ($ofertixProductVO->images() as $key => $image) {
//
//                $imageType = ($key == 0) ? 'Main' : 'PT'.$key;
//
//                $legacyProductParaJC[] = array('sku' => $ofertixProductVO->sku(),
//                    'image_type' => $imageType,
//                    'image_location' => $image->url()
//                );
//            }
//
//            $result = $this->updateByFeedType(MwsClient::MESSAGE_TYPE_PRODUCT_IMAGE, $legacyProductParaJC);
//
//            /** @var  $response \MarketplaceWebService_Model_SubmitFeedResponse */
//            $response = $result[0];
//            $xml = $result[1];
//            $this->getRequestData($response, $xml);
//            $this->handleThrottling($response);
//        }

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

        /** @var \DOMDocument $baseFeed */
        $baseFeed = $this->getMWSBaseFeed($feedTypeObjects[0], $clear);

        /** @var AmazonFeedTypeInterface $feedTypeObject */
        foreach ($feedTypeObjects as $feedTypeObject) {
            try {
                /** @var \SimpleXMLElement $feed */
                $feed = $feedTypeObject->xmlNode();
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
     * @param AmazonFeedTypeInterface $feedType
     * @param bool|false $clearReplace
     *
     * @return \DOMDocument
     */
    private function getMWSBaseFeed(AmazonFeedTypeInterface $feedType, $clearReplace = false)
    {
        $messageType = $feedType->feedType();
        $mwsXmlHeader = <<<HERE_DOC
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" />
HERE_DOC;

        $feedXml = new \SimpleXMLElement($mwsXmlHeader);
        $header = $feedXml->addChild('Header');
        $header->addChild('DocumentVersion', '1.01');
        $header->addChild('MerchantIdentifier', $this->config['merchant_id']);
        $feedXml->addChild('MessageType', $messageType);
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
     * @param $feedType
     * @param AmazonProduct $amazonProduct
     *
     * @return \SimpleXMLElement|String
     * @throws \Exception
     */
    private function getNodeByType($feedType, AmazonProduct $amazonProduct)
    {
        $feedBuilder = new FeedBuilder($feedType, $amazonProduct);
        $feed = '';
        if (!empty($amazonProduct->sku())) {
            switch ($feedType) {
                case self::FEED_TYPE_PRODUCT:
                    $feed = $feedBuilder->getProductNode();
                    break;
                case self::FEED_TYPE_INVENTORY:
                    $feed =  $feedBuilder->getInventoryNode();
                    break;
                case self::FEED_TYPE_PRICING:
                    $feed =  $feedBuilder->getPriceNode();
                    break;
                case self::FEED_TYPE_PRODUCT_IMAGE:
                    $feed =  $feedBuilder->getProductImageNode();
                    break;
                case self::FEED_TYPE_RELATIONSHIP:
                    $feed =  $feedBuilder->getRelationshipNode();
                    break;
            }
            $validated = self::validateFeed($feed->asXML(), $feedType);

            if ($validated === true) {
                return $feed;
            }
            throw new \Exception('Xml is not valid!');
        }
        throw new \Exception('SKU is a mandatory field!');
    }

    /**
     * @param $feed
     * @param $feedType
     *
     * @return bool
     */
    private function validateFeed($feed, $feedType)
    {
        $valid = false;
        $pathXSD = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.
            DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.self::XSD_DIR.
            DIRECTORY_SEPARATOR.$feedType.'.xsd';
        if (file_exists($pathXSD)) {
            $xmlfeed = new \DOMDocument();
            $xmlfeed->resolveExternals = true;
            $xmlfeed->strictErrorChecking = true;
            $xmlfeed->preserveWhiteSpace = false;
            $xmlfeed->formatOutput = false;
            $xmlfeed->loadXML($feed);
            $valid = $xmlfeed->schemaValidate($pathXSD);
        }

        return $valid;
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
     *
     * @return \MarketplaceWebService_Model_SubmitFeedResponse
     */
    private function submitFeed(\DOMDocument $feed, $marketPlaceId = 'default')
    {
        $marketPlaceId = $marketPlaceId === 'default' ? $this->config['marketplace_id'] : $marketPlaceId;

        /** @var  $request  \MarketplaceWebService_Model_SubmitFeedRequest */
        $request = self::getSubmitFeedRequest($feed, $marketPlaceId);

        try{
            $response = $this->client->submitFeed($request);

            return $response;
        } catch(\Exception $ex) {
            //ToDO: process exceptions
            var_dump($ex);
            die('** Error! **');
        }

    }

    /**
     * @param \DOMDocument $feed
     * @param $marketPlaceId
     *
     * @return \MarketplaceWebService_Model_SubmitFeedRequest
     */
    private function getSubmitFeedRequest(\DOMDocument $feed, $marketPlaceId)
    {
        file_put_contents('/tmp/mws', $feed->saveXML());
        $feedHandle = fopen('/tmp/mws', 'r');
        rewind($feedHandle);
        $messageType = $feed->getElementsByTagName('MessageType')->item(0)->nodeValue;
        $merchantID = $feed->getElementsByTagName('MerchantIdentifier')->item(0)->nodeValue;
        $feedType = '';
        switch ($messageType) {
            case self::FEED_TYPE_PRODUCT:
                $feedType = self::PRODUCT_FEED;
                break;
            case self::FEED_TYPE_PRICING:
                $feedType = self::PRICING_FEED;
                break;
            case self::FEED_TYPE_INVENTORY:
                $feedType = self::INVENTORY_FEED;
                break;
            case self::FEED_TYPE_PRODUCT_IMAGE:
                $feedType = self::PRODUCT_IMAGES_FEED;
                break;
            case self::FEED_TYPE_RELATIONSHIP:
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

        $amazonRequest = new $this->requestClass(
            $feedSubmissionInfo->getFeedSubmissionId(),
            $feedSubmissionInfo->getFeedType(),
            $feedSubmissionInfo->getSubmittedDate(),
            $feedSubmissionInfo->getFeedProcessingStatus(),
            $responseMetadata->getRequestId(),
            $feedObj->saveXML()
        );
        return $amazonRequest;
    }



//
//
//
//
//
//
//
//
//    /**
//     * @param UploadableProductInterface $ofertixProductVO
//     */
//    public function deleteProduct(UploadableProductInterface $ofertixProductVO)
//    {
//        $legacyProductParaJC = array('sku' => $ofertixProductVO->sku(),
//            'ean' => $ofertixProductVO->ean13()->ean13(),
//            'title' => $ofertixProductVO->title(),
//            'description' => $ofertixProductVO->description(),
//            'brand' => $ofertixProductVO->brand(),
//            'quantity' => $ofertixProductVO->stock(),
//            'price' => $ofertixProductVO->price(),
//            'relations' => array(),
//            'ItemType' => 'true'
//        );
//
//        $result = $this->updateByFeedType(MwsClient::MESSAGE_TYPE_PRODUCT,
//            array($legacyProductParaJC),
//            false,
//            MwsClient::OPERATION_TYPE_DELETE);
//
//        /** @var  $response \MarketplaceWebService_Model_SubmitFeedResponse */
//        $response = $result[0];
//        $xml = $result[1];
//        $this->getRequestData($response, $xml);
//        $this->handleThrottling($response);
//    }
//
//    /**
//     * @param UploadableProductInterface $ofertixProductVO
//     */
//    public function updateStock(UploadableProductInterface $ofertixProductVO)
//    {
//        $legacyProductParaJC = array('sku' => $ofertixProductVO->sku(),
//            'quantity' => $ofertixProductVO->stock()
//        );
//
//        $result = $this->updateByFeedType(MwsClient::MESSAGE_TYPE_INVENTORY, array($legacyProductParaJC));
//
//        /** @var  $response \MarketplaceWebService_Model_SubmitFeedResponse */
//        $response = $result[0];
//        $xml = $result[1];
//        $this->getRequestData($response, $xml);
//        $this->handleThrottling($response);
//    }
//
//    /**
//     * @param UploadableProductInterface $ofertixProductVO
//     */
//    public function updatePrice(UploadableProductInterface $ofertixProductVO)
//    {
//        $legacyProductParaJC = array('sku' => $ofertixProductVO->sku(),
//            'standard_price' => $ofertixProductVO->price()
//        );
//
//        $result = $this->updateByFeedType(MwsClient::MESSAGE_TYPE_PRICING, array($legacyProductParaJC));
//
//        /** @var  $response \MarketplaceWebService_Model_SubmitFeedResponse */
//        $response = $result[0];
//        $xml = $result[1];
//        $this->getRequestData($response, $xml);
//        $this->handleThrottling($response);
//    }
//
//    /**
//     * @param UploadableProductInterface $ofertixProductVO
//     */
//    public function updateProductImages(UploadableProductInterface $ofertixProductVO)
//    {
//        foreach ($ofertixProductVO->images() as $key => $image) {
//
//            $imageType = ($key == 0) ? 'Main' : 'PT'.$key;
//
//            $legacyProductParaJC[] = array('sku' => $ofertixProductVO->sku(),
//                'image_type' => $imageType,
//                'image_location' => $image->url()
//            );
//        }
//
//        $result = $this->updateByFeedType(MwsClient::MESSAGE_TYPE_PRODUCT_IMAGE, $legacyProductParaJC);
//
//        /** @var  $response \MarketplaceWebService_Model_SubmitFeedResponse */
//        $response = $result[0];
//        $xml = $result[1];
//        $this->getRequestData($response, $xml);
//        $this->handleThrottling($response);
//    }
//
//    public function updateOverrideShippingRates()
//    {
//
//    }
//
//    public function reviewProcessingResults()
//    {
//
//    }

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
            echo 'Has been reached the limit of requests. Waiting 5 minutes to continue...';
            sleep(5 * 60);
        }
    }


}
