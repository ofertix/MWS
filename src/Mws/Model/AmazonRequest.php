<?php

namespace Ofertix\Mws\Model;

class AmazonRequest
{
    protected $id;
    protected $feedSubmissionId;
    protected $feedType;
    protected $submittedDate;
    protected $status;
    protected $requestId;
    protected $startedProcessingDate;
    protected $completedProcessingDate;
    protected $xml;

    public function __construct($feedSubmissionId, $feedType, $submittedDate, $status, $requestId, $xml)
    {
        $this->feedSubmissionId = $feedSubmissionId;
        $this->feedType = $feedType;
        $this->submittedDate = $submittedDate;
        $this->status = $status;
        $this->requestId = $requestId;
        $this->xml = $xml;
    }

    /**
     * Get Id
     *
     * @return mixed
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return AmazonRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get FeedSubmissionId
     *
     * @return mixed
     */
    public function feedSubmissionId()
    {
        return $this->feedSubmissionId;
    }

    /**
     * @param mixed $feedSubmissionId
     *
     * @return AmazonRequest
     */
    public function setFeedSubmissionId($feedSubmissionId)
    {
        $this->feedSubmissionId = $feedSubmissionId;

        return $this;
    }

    /**
     * Get FeedType
     *
     * @return mixed
     */
    public function feedType()
    {
        return $this->feedType;
    }

    /**
     * @param mixed $feedType
     *
     * @return AmazonRequest
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;

        return $this;
    }

    /**
     * Get SubmittedDate
     *
     * @return mixed
     */
    public function submittedDate()
    {
        return $this->submittedDate;
    }

    /**
     * @param mixed $submittedDate
     *
     * @return AmazonRequest
     */
    public function setSubmittedDate($submittedDate)
    {
        $this->submittedDate = $submittedDate;

        return $this;
    }

    /**
     * Get Status
     *
     * @return mixed
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return AmazonRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get RequestId
     *
     * @return mixed
     */
    public function requestId()
    {
        return $this->requestId;
    }

    /**
     * @param mixed $requestId
     *
     * @return AmazonRequest
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * Get StartedProcessingDate
     *
     * @return mixed
     */
    public function startedProcessingDate()
    {
        return $this->startedProcessingDate;
    }

    /**
     * @param mixed $startedProcessingDate
     *
     * @return AmazonRequest
     */
    public function setStartedProcessingDate($startedProcessingDate)
    {
        $this->startedProcessingDate = $startedProcessingDate;

        return $this;
    }

    /**
     * Get CompletedProcessingDate
     *
     * @return mixed
     */
    public function completedProcessingDate()
    {
        return $this->completedProcessingDate;
    }

    /**
     * @param mixed $completedProcessingDate
     *
     * @return AmazonRequest
     */
    public function setCompletedProcessingDate($completedProcessingDate)
    {
        $this->completedProcessingDate = $completedProcessingDate;

        return $this;
    }

    /**
     * Get Xml
     *
     * @return mixed
     */
    public function xml()
    {
        return $this->xml;
    }

    /**
     * @param mixed $xml
     *
     * @return AmazonRequest
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }
}
