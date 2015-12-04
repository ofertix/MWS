<?php

class ResponseHeaderMetadata extends MarketplaceWebService_Model_ResponseHeaderMetadata
{
    const QUOTA_MAX = 'x-mws-quota-max';
    const QUOTA_REMAINING = 'x-mws-quota-remaining';
    const QUOTA_RESETS_AT = 'x-mws-quota-resetsOn';

    public function __construct($requestId = null, $responseContext = null, $timestamp = null,
        $quotaMax = null, $quotaMax = null, $quotaResetsAt = null) {
        parent::__construct();

        $this->metadata[self::QUOTA_MAX] = $quotaMax;
        $this->metadata[self::QUOTA_REMAINING] = $quotaMax;
        $this->metadata[self::QUOTA_RESETS_AT] = $quotaResetsAt;
    }

    /**
     * Gets the max quota allowed for a quota period
     * (from the x-mws-quota-max header)
     *
     * @return the max quota allowed for a quota period
     */
    public function getQuotaMax() {
        return $this->metadata[self::QUOTA_MAX];
    }

    /**
     * Gets the quota remaining within this quota period
     * (from the x-mws-quota-remaining header)
     *
     * @return the quota remaining within this quota period
     */
    public function getQuotaRemaining() {
        return $this->metadata[self::QUOTA_REMAINING];
    }

    /**
     * Gets the time that this quota period ends
     * (from the x-mws-quota-resetsOn header)
     *
     * @return the time that this quota period ends
     */
    public function getQuotaResetsAt() {
        return $this->metadata[self::QUOTA_RESETS_AT];
    }

    public function __toString() {
        return "RequestId: " . $this->getRequestId() . ", ResponseContext: " . $this->getResponseContext() .
        ", Timestamp: " . $this->getTimestamp() . ", Quota Max: " . $this->getQuotaMax() .
        ", Quota Remaining: " . $this->getQuotaRemaining() . ", Quota Resets At: " . $this->getQuotaResetsAt();
    }

}
