<?php

namespace InstanteTests\Application\Responses;

use Instante\Application\Responses\VirtualFileResponse;
use Nette\Http\IRequest;
use Nette\Http\Response;
use Nette\Utils\Callback;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class VirtualFileResponseTest extends TestCase
{
    public function testResponseCreation()
    {
        $r = new VirtualFileResponse('theName', function () { return 'theCallback'; }, 'theContentType');
        Assert::equal('theName', $r->getName());
        Assert::equal('theCallback', Callback::invoke($r->getOutputGenerator()));
        Assert::equal('theContentType', $r->getContentType());
    }

    public function testResponseDownload()
    {
        $r = new VirtualFileResponse('theName', function () { echo 'aaa'; }, 'theContentType');
        $response = new Response();
        ob_start();
        $r->send(new MockRequest(), $response);
        ob_end_clean();
        Assert::equal('attachment; filename="theName"', $response->headers['Content-Disposition'], "Setting content disposition download");
        Assert::equal('bytes', $response->headers['Accept-Ranges'], "Setting Accept-Ranges header");
        Assert::equal('3', $response->headers['Content-Length'], "Calculating Content-Length");
    }

    public function testResponseInline()
    {
        $r = new VirtualFileResponse('theName', function () { echo 'aaa'; }, 'theContentType', FALSE);
        $response = new Response();
        ob_start();
        $r->send(new MockRequest(), $response);
        ob_end_clean();
        Assert::equal('inline; filename="theName"', $response->headers['Content-Disposition'], "Setting content disposition inline");

    }
}

class MockRequest implements IRequest
{
    function getUrl() { return NULL; }

    function getQuery($key = NULL, $default = NULL) { return NULL; }

    function getPost($key = NULL, $default = NULL) { return NULL; }

    function getFile($key) { return NULL; }

    function getFiles() { return NULL; }

    function getCookie($key, $default = NULL) { return NULL; }

    function getCookies() { return NULL; }

    function getMethod() { return NULL; }

    function isMethod($method) { return NULL; }

    function getHeader($header, $default = NULL) { return NULL; }

    function getHeaders() { return NULL; }

    function isSecured() { return NULL; }

    function isAjax() { return NULL; }

    function getRemoteAddress() { return NULL; }

    function getRemoteHost() { return NULL; }

    function getRawBody() { return NULL; }
}

run(new VirtualFileResponseTest);
