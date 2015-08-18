<?php

namespace InstanteTests\Application\Responses;

use Instante\Application\Responses\StringResponse;
use Instante\Application\Responses\VirtualFileResponse;
use Nette\Http\IRequest;
use Nette\Http\Response;
use Nette\Utils\Callback;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class StringResponseTest extends TestCase
{
    public function testResponse()
    {
        $r = new StringResponse('PayLoad', 'foo/bar');
        Assert::equal('PayLoad', $r->getPayload());
        Assert::equal('foo/bar', $r->getContentType());
        $response = new Response;
        ob_start();
        $r->send(new MockRequest, $response);
        $o = ob_get_clean();
        Assert::equal('PayLoad', $o);
        Assert::equal('foo/bar', $response->getHeader('Content-Type'));
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

run(new StringResponseTest);
