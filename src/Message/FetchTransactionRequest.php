<?php

namespace Omnipay\Neteller\Message;

use GuzzleHttp\Exception\BadResponseException;
use Omnipay\Common\Exception\InvalidRequestException;


/**
 * Neteller Fetch Transaction Request.
 *
 * @author    Alexander Fedra <contact@dercoder.at>
 * @copyright 2016 DerCoder
 * @license   http://opensource.org/licenses/mit-license.php MIT
 */
class FetchTransactionRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getData()
    {
        if ($transactionId = $this->getTransactionId()) {

            return array(
                'id'      => (string) $transactionId,
                'refType' => 'merchantRefId'
            );

        } elseif ($transactionReference = $this->getTransactionReference()) {

            return array(
                'id' => (string) $transactionReference
            );

        } else {

            throw new InvalidRequestException('The transactionId or transactionReference parameter is required');

        }
    }

    /**
     * @param array $data
     *
     * @return FetchTransactionResponse
     */
    public function sendData($data)
    {
        $headers = array(
            'Content-Type'  => 'application/json',
            'Authorization' => $this->createBearerAuthorization()
        );

        $id = $data['id'];
        unset($data['id']);

        $uri = $this->createUri('payments/' . $id, $data);

        try {
            $response = $this->httpClient->request('GET', $uri, $headers);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        $jsonResponse = json_decode($response->getBody()->__toString(), true);
        return new FetchTransactionResponse($this, $jsonResponse);
    }
}
