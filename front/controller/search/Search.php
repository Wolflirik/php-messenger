<?php

namespace front\controller\search;

class Search extends \system\Controller {
    public function index() {
        $this->template = 'search/search';
        $this->setOutput();
    }
}