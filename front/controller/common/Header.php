<?php

namespace front\controller\common;

class Header extends \system\Controller{

    public function index()
    {
        $this->data['title'] = $this->document->getTitle();
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['full_name'] = $this->user->getFullName();

        $this->data['is_messenger'] = isset($this->request->get['is_messenger']);

        $this->data['logged'] = $this->user->isLogged();

        $this->data['user'] = $this->user->getFullName();

        $this->template = 'common/header';

        $this->render();
    }
}