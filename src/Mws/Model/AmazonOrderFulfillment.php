<?php

namespace Ofertix\Mws\Model;


/**
 * Class AmazonOrderFulfillment
 * @package Ofertix\Mws\Model
 */
class AmazonOrderFulfillment implements AmazonFeedTypeInterface
{
    const FEED_NAME = 'OrderFulfillment';


    use AmazonFeedTypeTrait;

    /** @var  string */
    protected $amazonOrderID;
    /** @var  string */
    protected $merchantOrderID;
    /** @var  string */
    protected $merchantFulfillmentID;
    /** @var  DateTime */
    protected $fulfillmentDate;
    /** @var  string */
    protected $carrierCode;
    /** @var  string */
    protected $shippingMethod;
    /** @var  string */
    protected $shipperTrackingNumber;
    /** @var  array */
    protected $items;

    /**
     * AmazonOrderFulfillment constructor.
     * @param $amazonOrderID
     * @param $fulfillmentDate
     * @param $carrierCode
     * @param $shippingMethod
     * @param $shipperTrackingNumber
     * @param array $items
     * @param null $merchantOrderID
     * @param null $merchantFulfillmentID
     */
    public function __construct(
        $amazonOrderID,
        $fulfillmentDate,
        $carrierCode,
        $shippingMethod,
        $shipperTrackingNumber,
        $merchantOrderID = null,
        $merchantFulfillmentID = null,
        $items = []
    ) {
        $this->amazonOrderID = $amazonOrderID;
        $this->items = $items;
        $this->fulfillmentDate = $fulfillmentDate;
        $this->carrierCode = $carrierCode;
        $this->shippingMethod = $shippingMethod;
        $this->shipperTrackingNumber = $shipperTrackingNumber;
        $this->merchantOrderID = $merchantOrderID;
        $this->merchantFulfillmentID = $merchantFulfillmentID;

    }

    public function addItem(
        $amazonOrderItemId,
        $quantity,
        $orderItemId,
        $merchantFulfillmentItemID
    ) {
        $this->items[$orderItemId] = new AmazonOrderFulfillmentItem(
            $amazonOrderItemId,
            $quantity,
            $orderItemId,
            $merchantFulfillmentItemID
        );

    }


    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('AmazonOrderID', $this->amazonOrderID());
        //$rootNode->addChild('MerchantOrderID', $this->merchantOrderID());
        $rootNode->addChild('MerchantFulfillmentID', $this->merchantFulfillmentID());
        $rootNode->addChild('FulfillmentDate', $this->fulfillmentDate());
        $fulfillmentDate = $rootNode->addChild('FulfillmentData');
        $fulfillmentDate->addChild('CarrierCode', $this->carrierCode());
        $fulfillmentDate->addChild('ShippingMethod', $this->shippingMethod());
        $fulfillmentDate->addChild('ShipperTrackingNumber', $this->shipperTrackingNumber());

        foreach ($this->items() as $item) {
            $ItemNode = $rootNode->addChild('Item');
            $ItemNode->addChild('AmazonOrderItemCode', $item->amazonOrderItemCode());
            // $ItemNode->addChild('MerchantOrderItemID', $item->merchantOrderItemID());
            $ItemNode->addChild('MerchantFulfillmentItemID', $item->merchantFulfillmentItemID());
            $ItemNode->addChild('Quantity', $item->quantity());
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

}
