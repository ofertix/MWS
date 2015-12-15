<?php

class FeedClient extends MarketplaceWebService_Client
{

    /**
     * Método extendido del original para añadirle los datos de cuota en el header metadata.
     * Setup and execute the request via cURL and return the server response.
     *
     * @param $action - the MWS action to perform.
     * @param $converted - the MWS parameters for the Action.
     * @param $dataHandle - A stream handle to either a feed to upload, or a report/feed submission result to download.
     * @param $contentMd5 - The Content-MD5 HTTP header value used for feed submissions.
     * @return array
     */
    protected function performRequest($action, array $converted, $dataHandle = null, $contentMd5 = null) {

        $curlOptions = $this->configureCurlOptions($action, $converted, $dataHandle, $contentMd5);

        if (is_null($curlOptions[CURLOPT_RETURNTRANSFER]) || !$curlOptions[CURLOPT_RETURNTRANSFER]) {
            $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        }

        $this->curlClient = curl_init();
        curl_setopt_array($this->curlClient, $curlOptions);

        $this->headerContents = @fopen('php://memory', 'rw+');
        $this->errorResponseBody = @fopen('php://memory', 'rw+');

        $httpResponse = curl_exec($this->curlClient);

        rewind($this->headerContents);
        $header = stream_get_contents($this->headerContents);

        $parsedHeader = $this->parseHttpHeader($header);

        require_once('Model/ResponseHeaderMetadata.php');
        $responseHeaderMetadata = new ResponseHeaderMetadata(
            $parsedHeader['x-mws-request-id'],
            $parsedHeader['x-mws-response-context'],
            $parsedHeader['x-mws-timestamp'],
            $parsedHeader['x-mws-quota-max'],
            $parsedHeader['x-mws-quota-remaining'],
            $parsedHeader['x-mws-quota-resetson']);

        $code = (int) curl_getinfo($this->curlClient, CURLINFO_HTTP_CODE);

        // Only attempt to verify the Content-MD5 value if the request was successful.
        if (RequestType::getRequestType($action) === RequestType::POST_DOWNLOAD) {
            if ($code != 200) {
                rewind($this->errorResponseBody);
                $httpResponse =  stream_get_contents($this->errorResponseBody);
            } else {
                $this->verifyContentMd5($this->getParsedHeader($parsedHeader,'Content-MD5'), $dataHandle);
                $httpResponse = $this->getDownloadResponseDocument($action, $parsedHeader);
            }
        }

        // Cleanup open streams and cURL instance.
        @fclose($this->headerContents);
        @fclose($this->errorResponseBody);
        curl_close($this->curlClient);


        return array (
            'Status' => $code,
            'ResponseBody' => $httpResponse,
            'ResponseHeaderMetadata' => $responseHeaderMetadata);
    }
}
