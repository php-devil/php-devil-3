<?php
namespace PhpDevil\framework\components\userdata;

class Get extends AbstractUserData
{
    protected function initAfterConstruct()
    {
        $this->source = filter_var($_GET, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    }
}