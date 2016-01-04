<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonOrderAcknowledgement
 * @package Ofertix\Mws\Model
 */
class AmazonOrderAcknowledgement implements AmazonFeedTypeInterface
{
    const FEED_NAME = 'OrderAcknowledgement';

    use AmazonFeedTypeTrait;

    /** @var  string */
    protected $amazonOrderID;
    /** @var  string */
    protected $merchantOrderID;
    /** @var  string */
    protected $statusCode;
    /** @var  array */
    protected $items;


    public function __construct(
        $amazonOrderID,
        $statusCode,
        $merchantOrderID = null,
        $items = []
    ) {
        $this->amazonOrderID = $amazonOrderID;
        $this->merchantOrderID = $merchantOrderID;
        $this->statusCode = ($statusCode === true)? 'Sucess' : 'Failure';
        $this->items = $items;

    }

    public function addItem($amazonOrderItemId, $cancelReason ) {

        $orderItemObj  = new AmazonOrderFulfillmentItem($amazonOrderItemId);
        $orderItemObj->setCancelReason($cancelReason);
        $this->items[] = $orderItemObj;
    }

    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('AmazonOrderID', $this->amazonOrderID());
        //$rootNode->addChild('MerchantOrderID', $this->merchantOrderID());
        $rootNode->addChild('StatusCode', $this->statusCode());
        foreach ($this->items() as $item) {
            $ItemNode = $rootNode->addChild('Item');
            $ItemNode->addChild('AmazonOrderItemCode', $item->amazonOrderItemCode());
            $ItemNode->addChild('CancelReason', $item->cancelReason());
        }

        return $rootNode;
    }


    /**
     * Get AmazonOrderID
     *
     * @return string
     */
    public function amazonOrderID()
    {
        return $this->amazonOrderID;
    }

    /**
     * @param string $amazonOrderID
     * @return AmazonOrderFulfillment
     */
    public function setAmazonOrderID($amazonOrderID)
    {
        $this->amazonOrderID = $amazonOrderID;
        return $this;
    }

    /**
     * Get MerchantFulfillmentID
     *
     * @return string
     */
    public function merchantFulfillmentID()
    {
        return $this->merchantFulfillmentID;
    }

    /**
     * @param string $merchantFulfillmentID
     * @return AmazonOrderFulfillment
     */
    public function setMerchantFulfillmentID($merchantFulfillmentID)
    {
        $this->merchantFulfillmentID = $merchantFulfillmentID;
        return $this;
    }

    /**
     * Get FulfillmentDate
     *
     * @return datetime
     */
    public function fulfillmentDate()
    {
        return $this->fulfillmentDate;
    }

    /**
     * @param datetime $fulfillmentDate
     * @return AmazonOrderFulfillment
     */
    public function setFulfillmentDate($fulfillmentDate)
    {
        $this->fulfillmentDate = $fulfillmentDate;
        return $this;
    }

    /**
     * Get CarrierCode
     *
     * @return string
     */
    public function carrierCode()
    {
        return $this->carrierCode;
    }

    /**
     * @param string $carrierCode
     * @return AmazonOrderFulfillment
     */
    public function setCarrierCode($carrierCode)
    {
        $this->carrierCode = $carrierCode;
        return $this;
    }

    /**
     * Get ShippingMethod
     *
     * @return string
     */
    public function shippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     * @return AmazonOrderFulfillment
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
        return $this;
    }

    /**
     * Get ShipperTrackingNumber
     *
     * @return string
     */
    public function shipperTrackingNumber()
    {
        return $this->shipperTrackingNumber;
    }

    /**
     * @param string $shipperTrackingNumber
     * @return AmazonOrderFulfillment
     */
    public function setShipperTrackingNumber($shipperTrackingNumber)
    {
        $this->shipperTrackingNumber = $shipperTrackingNumber;
        return $this;
    }

    /**
     * Get Items
     *
     * @return mixed
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     * @return AmazonOrderFulfillment
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get MerchantOrderID
     *
     * @return string
     */
    public function merchantOrderID()
    {
        return $this->merchantOrderID;
    }

    /**
     * @param string $merchantOrderID
     * @return AmazonOrderFulfillment
     */
    public function setMerchantOrderID($merchantOrderID)
    {
        $this->merchantOrderID = $merchantOrderID;
        return $this;
    }

    /**
     * Get StatusCode
     *
     * @return string
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusCode
     * @return AmazonOrderAcknowledgement
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

}
