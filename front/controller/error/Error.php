<?php

namespace front\controller\error;

class Error extends \system\Controller{

    public function index() {
        $this->data['text_error'] = 'Ошибка 404';

        $link = $this->user->isLogged() ? '' : 'login';
        $this->data['text_error_description'] = 'Произошла ошибка! Видимо такой страницы не существует, попробуйте перейти на <a href="' . $this->config->get('main', 'domain') . '/' . $link . '">главную</a>.';

        $this->document->setTitle($this->data['text_error']);

        $this->child = [
            'common/header',
            'common/footer'
        ];

        $this->template = 'error/error';
        $this->setHeader('Status: 404');
        $this->setOutput();
    }
}