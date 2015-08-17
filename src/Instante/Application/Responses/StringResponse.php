<?php

namespace Instante\Application\Responses;

use Nette;

/**
 * Sends a string with custom content type.
 *
 * @author     Richard Ejem
 *
 * @property-read string $payload
 * @property-read string $contentType
 */
class StringResponse extends Nette\Object implements Nette\Application\IResponse
{

    /** @var string */
    private $payload;

    /** @var string */
    private $contentType;

    /**
     * @param  string  payload
     * @param  string  MIME content type
     */
    public function __construct($payload, $contentType = NULL)
    {
        if (!is_string($payload)) {
            throw new Nette\InvalidArgumentException;
        }
        $this->payload = $payload;
        $this->contentType = $contentType ? $contentType : 'application/json';
    }

    public static function json($payload)
    {
        return new static($payload, 'application/json');
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Returns the MIME content type of a downloaded file.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sends response to output.
     * @return void
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
    {
        $httpResponse->setContentType($this->contentType);
        $httpResponse->setExpiration(FALSE);
        echo $this->payload;
    }

}
