<?php
namespace PhpDevil\framework\components\userdata;

class Post extends AbstractUserData
{
    protected function initAfterConstruct()
    {
        $this->source = filter_var($_POST, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    }
}