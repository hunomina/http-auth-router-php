<?php

namespace hunomina\Routing\Auth\Test;

use hunomina\Http\Response\HtmlResponse;

class TestController
{
    /**
     * @return HtmlResponse
     */
    public function index(): HtmlResponse
    {
        return new HtmlResponse('ok');
    }
}