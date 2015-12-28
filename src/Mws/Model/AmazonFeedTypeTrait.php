<?php

namespace Ofertix\Mws\Model;

trait AmazonFeedTypeTrait
{

    public static $feeds = [
        "product",
        "price",
        "inventory",
        "pricing"
    ];


    public static $statusList = [
          'pending' => [
             'SUBMITTED' => '_SUBMITTED_',
             'IN_PROGRESS' => '_IN_PROGRESS_'
            ],
           'cancel' => [ 'CANCELLED' => '_CANCELLED_'],
           'done' => [ 'DONE' => '_DONE_'],
        ];
    public static $feedTypes = [
            "product" => "_POST_PRODUCT_DATA_",
            "relationship" => "_POST_PRODUCT_RELATIONSHIP_DATA_",
            "item" => "_POST_ITEM_DATA_",
            "ProductImage"=>"_POST_PRODUCT_IMAGE_DATA_",
            "pricing"=>"_POST_PRODUCT_PRICING_DATA_",
            "inventory" =>"_POST_INVENTORY_AVAILABILITY_DATA_",
            "acknowledment" => "_POST_ORDER_ACKNOWLEDGEMENT_DATA_",
            "fullfillment" => "_POST_ORDER_FULFILLMENT_DATA_",
            "adjustment "=> "_POST_PAYMENT_ADJUSTMENT_DATA_",
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
