<?php
namespace PhpDevil\framework\components\weburl;

class Request
{
    private $uri = '/';

    private $uriPointer = 0;

    public function getUnusedUri()
    {
        return substr($this->uri, $this->uriPointer);
    }

    public function __construct()
    {
        $request = parse_url(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
        $this->uri = $request['path'];
        if (isset($request['query'])) {

        }

    }
}