<?php

namespace Ofertix\Mws\Model;

trait AmazonFeedTypeTrait
{


    public static $statusList = [
          'pending' => [
             'SUBMITTED' => '_SUBMITTED_',
             'IN_PROGRESS' => '_IN_PROGRESS_'
            ],
           'cancel' => [ 'CANCELLED' => '_CANCELLED_'],
           'done' => [ 'DONE' => '_DONE_'],
        ];
    public static $feedTypes = [
            "Product" => "_POST_PRODUCT_DATA_",
            "Relationship" => "_POST_PRODUCT_RELATIONSHIP_DATA_",
            "Item" => "_POST_ITEM_DATA_",
            "ProductImage"=>"_POST_PRODUCT_IMAGE_DATA_",
            "Price"=>"_POST_PRODUCT_PRICING_DATA_",
            "Inventory" =>"_POST_INVENTORY_AVAILABILITY_DATA_",
            "OrderAcknowledment" => "_POST_ORDER_ACKNOWLEDGEMENT_DATA_",
            "OrderFulfillment" => "_POST_ORDER_FULFILLMENT_DATA_",
            "OrderAdjustment "=> "_POST_PAYMENT_ADJUSTMENT_DATA_",
];

    public function getProductFeedTypes()
    {
        return self::$feedTypes['product'];
    }

    public function getFeedTypes()
    {
        return array_values( self::$feedTypes );
    }

    public function getPendingStatusList()
    {
        return self::$statusList['pending'];
    }

    public function getFeedByName($feedName)
    {
        return isset(self::$feedTypes[$feedName]) ? self::$feedTypes[$feedName] : false;
    }


    public function feedName()
    {
        return ucfirst(self::FEED_NAME);
    }

    public function feedType()
    {
        return self::getFeedByName($this->feedName());
    }


}
