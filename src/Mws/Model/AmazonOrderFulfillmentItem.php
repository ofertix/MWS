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
}
