<?php

/**
 * Class ProductFinder
 */
class ProductFinder
{

    public $client;

    public function __construct($config)
    {
        $this->client = \MwsClient::getProductClient($config);
        $this->config = $config;
    }

    public function getProductBySKU($sku) {
        $getMatchingProductForIdRequest = new \MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();
        $getMatchingProductForIdRequest->setSellerId($this->config['merchant_id']);
        $getMatchingProductForIdRequest->setMarketplaceId($this->config['marketplace_id']);
        $getMatchingProductForIdRequest->setIdType(\MwsClient::PRODUCT_ID_TYPE_SELLER_SKU);

        $idListType = new \MarketplaceWebServiceProducts_Model_IdListType();
        $idListType->setId($sku);

        $getMatchingProductForIdRequest->withIdList($idListType);
        return $this->client->getMatchingProductForId($getMatchingProductForIdRequest);
    }

    public function productExistsBySKU($sku)
    {
        $response = $this->getProductBySKU($sku);

        return $response->getGetMatchingProductForIdResult()[0]->getStatus() == 'Success' ? true: false;
    }


}
