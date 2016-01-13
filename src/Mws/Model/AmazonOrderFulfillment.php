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
    protected $carrierName;
    /** @var  string */
    protected $shippingMethod;
    /** @var  string */
    protected $shipperTrackingNumber;
    /** @var  array */
    protected $items;

    /**
     * @param $amazonOrderID
     * @param $carrierName
     * @param $shippingMethod
     * @param $shipperTrackingNumber
     * @param array $items
     */
    public function __construct(
        $amazonOrderID,
        $carrierName,
        $shippingMethod,
        $shipperTrackingNumber,
        $items = array()
    ) {
        $this->amazonOrderID = $amazonOrderID;
        $this->carrierName = $carrierName;
        $this->shippingMethod = $shippingMethod;
        $this->shipperTrackingNumber = $shipperTrackingNumber;
        $this->fulfillmentDate = new \DateTime('now');
    }


    /**
     * @return \SimpleXMLElement|String
     */
    public function xmlNode()
    {
        $rootNode = new \SimpleXMLElement('<'.$this->feedName().'></'.$this->feedName().'>');
        $rootNode->addChild('AmazonOrderID', $this->amazonOrderID());
        if ($this->merchantOrderID()!== null) {
            $rootNode->addChild('MerchantOrderID', $this->merchantOrderID());
        }
        if ($this->merchantFulfillmentID()!== null) {
            $rootNode->addChild('MerchantFulfillmentID', $this->merchantFulfillmentID());
        }
        $rootNode->addChild('FulfillmentDate', $this->fulfillmentDate());
        $fulfillmentDate = $rootNode->addChild('FulfillmentData');
        $fulfillmentDate->addChild('CarrierName', $this->carrierName());
        $fulfillmentDate->addChild('ShippingMethod', $this->shippingMethod());
        $fulfillmentDate->addChild('ShipperTrackingNumber', $this->shipperTrackingNumber());

        foreach ($this->items() as $item) {
            $ItemNode = $rootNode->addChild('Item');
            $ItemNode->addChild('AmazonOrderItemCode', $item->amazonOrderItemCode());
            // $ItemNode->addChild('MerchantOrderItemID', $item->merchantOrderItemID());
//            $ItemNode->addChild('MerchantFulfillmentItemID', $item->merchantFulfillmentItemID());
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
     * Set AmazonOrderID
     *
     * @param string $amazonOrderID
     *
     * @return AmazonOrderFulfillment
     */
    public function setAmazonOrderID($amazonOrderID)
    {
        $this->amazonOrderID = $amazonOrderID;

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
     * Set MerchantOrderID
     *
     * @param string $merchantOrderID
     *
     * @return AmazonOrderFulfillment
     */
    public function setMerchantOrderID($merchantOrderID)
    {
        $this->merchantOrderID = $merchantOrderID;

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
     * Set MerchantFulfillmentID
     *
     * @param string $merchantFulfillmentID
     *
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
     * @return DateTime
     */
    public function fulfillmentDate()
    {
        return $this->fulfillmentDate->format(\DateTime::W3C);
    }

    /**
     * Set FulfillmentDate
     *
     * @param DateTime $fulfillmentDate
     *
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
     * Set CarrierCode
     *
     * @param string $carrierCode
     *
     * @return AmazonOrderFulfillment
     */
    public function setCarrierCode($carrierCode)
    {
        $this->carrierCode = $carrierCode;

        return $this;
    }

    /**
     * Get CarrierName
     *
     * @return string
     */
    public function carrierName()
    {
        return $this->carrierName;
    }

    /**
     * Set CarrierName
     *
     * @param string $carrierName
     *
     * @return AmazonOrderFulfillment
     */
    public function setCarrierName($carrierName)
    {
        $this->carrierName = $carrierName;

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
     * Set ShippingMethod
     *
     * @param string $shippingMethod
     *
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
     * Set ShipperTrackingNumber
     *
     * @param string $shipperTrackingNumber
     *
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
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * Add Item
     *
     * @param AmazonOrderFulfillmentItem $item
     *
     * @return $this
     */
    public function addItem(AmazonOrderFulfillmentItem $item)
    {
        $this->items[] = $item;

        return $this;
    }
}
