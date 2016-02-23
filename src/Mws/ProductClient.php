<?php

namespace Ofertix\Mws;

use Ofertix\Mws\Model\Ean13;
use Ofertix\Mws\Model\Asin;
use Ofertix\Mws\Model\AmazonProduct;

class ProductClient
{

    private $config;
    private $client;
    private $class;

    /**
     * @param array $config
     *
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = MwsClientFactory::getClient($this->config, 'product');
        $this->class = isset($config['amazon_product_class']) ? $config['amazon_product_class'] :'\Ofertix\Mws\Model\AmazonProduct';
    }

    /**
     * @param Ean13 $ean
     *
     * @return AmazonProduct|bool
     */
    public function getProductByEAN(Ean13 $ean)
    {
        $getMatchingProductForIdRequest = new \MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();
        $getMatchingProductForIdRequest->setSellerId($this->config['merchant_id']);
        $getMatchingProductForIdRequest->setMarketplaceId($this->config['marketplace_id']);
        $getMatchingProductForIdRequest->setIdType('EAN');
        try {
            $idListType = new \MarketplaceWebServiceProducts_Model_IdListType();
            $idListType->setId($ean->ean13());
            $getMatchingProductForIdRequest->withIdList($idListType);
            /** @var $marketPlaceManager \MarketplaceWebServiceProducts_Model_GetMatchingProductForIdResponse */
            $response = $this->client->getMatchingProductForId($getMatchingProductForIdRequest);
            /** @var  $headersMetadata  \MarketplaceWebServiceProducts_Model_ResponseHeaderMetadata */
            $headersMetadata = $response->getResponseHeaderMetadata();
            //$quotaMax = $headersMetadata->getQuotaMax();
            $quotaRemaining = $headersMetadata->getQuotaRemaining();
            //$requestTimeStamp = $headersMetadata->getTimestamp();
            //$requestId = $headersMetadata->getRequestId();
            //$resetsAt = $headersMetadata->getQuotaResetsAt();
            while ($quotaRemaining <1) {
                echo 'ProductClient: Has been reached the limit of requests. Waiting 5 minutes to continue... '.'QuotaRemaining: '.$quotaRemaining;
                sleep(5*60);
            }
            if (is_object($response)) {
                $xmlString = str_replace('ns2:', '', $response->toXML());
                $xml = simplexml_load_string($xmlString);
                if (!isset($xml->{'GetMatchingProductForIdResult'}->Error)) {
                    $product = $xml->{'GetMatchingProductForIdResult'}->Products->Product;
                    $identifiers = $product->{'Identifiers'};
                    $asin = (!is_null($identifiers)) ? $identifiers->{'MarketplaceASIN'}->ASIN->__toString() : null;
                    $url = (!empty($asin)) ? 'http://www.amazon.es/gp/product/' . $asin : null;
                    try {
                        $asin = new Asin($asin);
                    } catch (\Exception $e) {
                        echo $e->getMessage()."\n";

                        return false;
                    }
                    $attributes = $product->{'AttributeSets'}->ItemAttributes;
                    $brand = $attributes->{'Brand'}->__toString();
                    $color = $attributes->{'Color'}->__toString();
                    $productType = $attributes->{'ProductTypeName'}->__toString();
                    $productGroup = $attributes->{'ProductGroup'}->__toString();
                    $size = $attributes->{'Size'}->__toString();
                    $model = $attributes->{'Model'}->__toString();
                    $title = $attributes->{'Title'}->__toString();
                    $prod = new $this->class($ean, $brand, $title);
                    $prod->setAsin($asin)
                        ->setColor($color)
                        ->setSize($size)
                        ->setUrl($url)
                        ->setProductType($productType)
                        ->setProductGroup($productGroup)
                        ->setModel($model);

                    return $prod;
                } else {
                    echo 'El ean '.$ean->ean13().' no está en Amazon.'."\n";

                    return false;
                }
            }
        } catch (\MarketplaceWebServiceProducts_Exception $ex) {
            var_dump($ex);
        }

        return false;
    }
}
