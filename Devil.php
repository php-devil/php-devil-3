<?php
class Devil extends \PhpDevil\framework\DevilBase
{

}

Devil::setPathOf('@devil',  __DIR__);
Devil::setPathOf('@vendor', dirname(dirname(__DIR__)));