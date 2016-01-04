<?php

namespace Ofertix\Mws\Model;

/**
 * Class AmazonOrderFulfillmentItem
 * @package Ofertix\Mws\Model
 */
class AmazonOrderFulfillmentItem
{
    /** @var  string */
    protected $amazonOrderItemCode;
    /** @var  string */
    protected $merchantOrderItemID;
    /** @var  string */
    protected $merchantFulfillmentItemID;
    /** @var  integer */
    protected $quantity;
    /** @var  string */
    protected $cancelReason;
    /** @var  string */
    protected $cancelReasons = [
        "NoInventory",
        "ShippingAddressUndeliverable",
        "CustomerExchange",
        "BuyerCanceled",
        "GeneralAdjustment",
        "CarrierCreditDecision",
        "RiskAssessmentInformationNotValid",
        "CarrierCoverageFailure",
        "CustomerReturn",
        "MerchandiseNotReceived"
    ];

    /**
     * Get AmazonOrderItemCode
     *
     * @return string
     */
    public function amazonOrderItemCode()
    {
        return $this->amazonOrderItemCode;
    }

    /**
     * @param string $amazonOrderItemCode
     * @return AmazonOrderFulfillmentItem
     */
    public function setAmazonOrderItemCode($amazonOrderItemCode)
    {
        $this->amazonOrderItemCode = $amazonOrderItemCode;
        return $this;
    }

    public function __construct(
        $amazonOrderItemId,
        $quantity = null,
        $orderItemId = null,
        $merchantFulfillmentItemID = null
    ) {
        $this->amazonOrderItemCode = $amazonOrderItemId;
        $this->quantity = $quantity;
        $this->merchantOrderItemID = $amazonOrderItemId;
        $this->merchantFulfillmentItemID = $orderItemId;

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

    /**
     * Get CancelReason
     *
     * @return string
     */
    public function cancelReason()
    {
        return $this->cancelReason;
    }

    /**
     * @param $cancelReason
     * @return $this|bool
     */
    public function setCancelReason($cancelReason)
    {
        if (in_array($cancelReason, $this->cancelReasons )) {
            $this->cancelReason = $cancelReason;
            return $this;
        } else {
            return false;
        }

    }

}
