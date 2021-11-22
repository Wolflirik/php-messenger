<?php

namespace front\controller\common;

class Footer extends \system\Controller{

    public function index()
    {
        $this->data['scripts'] = $this->document->getScripts();

        $this->template = 'common/footer';

        $this->render();
    }
}