<?php

namespace Engine\Http;


use Psr\Http\Message\ResponseInterface;

class ResponseSender
{
    /**
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response)
    {
        header(sprintf('HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $key => $values){
            foreach ($values as $value){
                header(sprintf('%s %s', $key, $value), false);
            }
        }

        echo $response->getBody()->getContents();
    }

}