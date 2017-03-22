<?php
namespace PhpDevil\framework\components\weburl;

class Request
{
    private $uri = '/';

    private $uriLength = 1;

    private $uriPointer = 1;

    public function getUnusedUri()
    {
        return substr($this->uri, $this->uriPointer);
    }

    public function getNext()
    {
        $nextUri = null;
        if ($this->uriPointer < $this->uriLength) {
            $unusedUri = $this->getUnusedUri();
            $nextSlash = strpos($unusedUri, '/');
            $nextUri = substr($unusedUri, 0, $nextSlash);
            $this->uriPointer += $nextSlash + 1;
        }
        return $nextUri;
    }

    public function __construct()
    {
        $request = parse_url(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
        $this->uri = str_replace('//', '/', $request['path'] . '/');
        $this->uriLength = mb_strlen($this->uri);
        if (isset($request['query'])) {

        }

    }
}