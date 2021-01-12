<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getCookie($cookie_name, $response)
    {
        $cookies = collect($response->headers->getCookies());
        return $cookies->filter(function ($cookie) use ($cookie_name) {
            return $cookie->getName() === $cookie_name;
        })->first();
    }

    protected function getXSRF()
    {
        return $this->getCookie('XSRF-TOKEN', $this->get('/sanctum/csrf-cookie'))->getValue();
    }
}
