<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonOrderFulfillmentItem
 * @package Ofertix\Mws\Model
 */
class AmazonOrderFulfillmentItem
{
    /** @var  string */
    protected $merchantOrderItemID;
    /** @var  string */
    protected $merchantFulfillmentItemID;
    /** @var  integer */
    protected $quantity;

    public function __construct(
        $amazonOrderItemId,
        $orderItemId,
        $quantity
    ) {
        $this->merchantOrderItemID = $amazonOrderItemId;
        $this->merchantFulfillmentItemID = $orderItemId;
        $this->quantity = $quantity;
    }

    /**
     * Get MerchantOrderItemID
     *
     * @return string
     */
    public function merchantOrderItemID()
    {
        return $this->merchantOrderItemID;
    }

    /**
     * @param string $merchantOrderItemID
     * @return AmazonOrderFulfillmentItem
     */
    public function setMerchantOrderItemID($merchantOrderItemID)
    {
        $this->merchantOrderItemID = $merchantOrderItemID;
        return $this;
    }

    /**
     * Get MerchantFulfillmentItemID
     *
     * @return string
     */
    public function merchantFulfillmentItemID()
    {
        return $this->merchantFulfillmentItemID;
    }

    /**
     * @param string $merchantFulfillmentItemID
     * @return AmazonOrderFulfillmentItem
     */
    public function setMerchantFulfillmentItemID($merchantFulfillmentItemID)
    {
        $this->merchantFulfillmentItemID = $merchantFulfillmentItemID;
        return $this;
    }

    /**
     * Get Quantity
     *
     * @return int
     */
    public function quantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return AmazonOrderFulfillmentItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

}
