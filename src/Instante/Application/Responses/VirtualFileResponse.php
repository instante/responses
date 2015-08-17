<?php

namespace Instante\Application\Responses;

use Nette;

/**
 * File download response with passing thru data from memory, not physical file
 *
 * @author     Richard Ejem
 *
 * @property-read string $outputGenerator
 * @property-read string $name
 * @property-read string $contentType
 */
class VirtualFileResponse extends Nette\Object implements Nette\Application\IResponse
{

    /** @var string */
    private $outputGenerator;

    /** @var string */
    private $contentType;

    /** @var string */
    private $name;

    /** @var bool */
    public $resuming = TRUE;

    /** @var bool */
    private $forceDownload;

    /** @var bool */
    private $precalculateFileSize;

    /**
     * @param  string   imposed file name
     * @param  callable output generator function, directly writing data to output
     * @param  string   MIME content type
     */
    public function __construct(
        $name,
        callable $outputGenerator,
        $contentType = NULL,
        $forceDownload = TRUE,
        $precalculateFileSize = TRUE
    ) {
        $this->outputGenerator = $outputGenerator;
        $this->name = $name;
        $this->contentType = $contentType ? $contentType : 'application/octet-stream';
        $this->forceDownload = $forceDownload;
        $this->precalculateFileSize = $precalculateFileSize;
    }

    /**
     * @return callable
     */
    public function getOutputGenerator()
    {
        return $this->outputGenerator;
    }

    /**
     * Returns the file name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        $httpResponse->setHeader('Content-Disposition', ($this->forceDownload ? 'attachment' : 'inline')
            . '; filename="' . $this->name . '"');

        $output = NULL;
        if ($this->precalculateFileSize) {
            ob_start();
            Nette\Utils\Callback::invokeArgs($this->outputGenerator);
            $output = ob_get_clean();

            $filesize = $length = strlen($output);
        }

        if ($this->resuming && $this->precalculateFileSize) {
            $httpResponse->setHeader('Accept-Ranges', 'bytes');
            if (preg_match('#^bytes=(\d*)-(\d*)\z#', $httpRequest->getHeader('Range'), $matches)) {
                list(, $start, $end) = $matches;
                if ($start === '') {
                    $start = max(0, $filesize - $end);
                    $end = $filesize - 1;
                } elseif ($end === '' || $end > $filesize - 1) {
                    $end = $filesize - 1;
                }
                if ($end < $start) {
                    $httpResponse->setCode(416); // requested range not satisfiable
                    return;
                }

                $httpResponse->setCode(206);
                $httpResponse->setHeader('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $filesize);
                $length = $end - $start + 1;
            } else {
                $httpResponse->setHeader('Content-Range', 'bytes 0-' . ($filesize - 1) . '/' . $filesize);
            }
        }

        if ($this->precalculateFileSize) {
            $httpResponse->setHeader('Content-Length', $length);
        }

        if (isset($start)) {
            echo substr($output, $start, $length);
        } elseif (isset($output)) {
            echo $output;
        } else {
            Nette\Utils\Callback::invoke($this->outputGenerator);
        }
    }

}
