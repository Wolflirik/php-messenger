<?php

namespace system\core\document;

use system\core\AbstractProvider;

class Provider extends AbstractProvider{

    public $name = 'document';

    public function init()
    {
        $document = new Document();
        $this->container->set($this->name, $document);
    }
}