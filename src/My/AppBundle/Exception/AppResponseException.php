<?php

namespace My\AppBundle\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppResponseException extends HttpException
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
        parent::__construct(200);
    }

    public function getResponse()
    {
        return $this->response;
    }
}
