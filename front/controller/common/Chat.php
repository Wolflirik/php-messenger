<?php

namespace front\controller\common;

class Chat extends \system\Controller{ 
    public function index() {
        if(!$this->user->isLogged()){
            $this->redirect($this->config->get('main', 'domain') . '/login');
        }

        $this->document->setTitle('Чат');
        $this->document->addScript($this->config->get('main', 'domain') . '/front/view/libs/websocket-reconnect.min.js');
        $this->document->addScript($this->config->get('main', 'domain') . '/front/view/libs/timeago.js');
        $this->document->addScript($this->config->get('main', 'domain') . '/front/view/libs/timeago_ru.js');
        $this->document->addScript($this->config->get('main', 'domain') . '/front/view/libs/cookie.js');
        $this->document->addScript($this->config->get('main', 'domain') . '/front/view/js/profile.js');

        $this->request->get['is_messenger'] = true;

        $this->child = [
            'common/header',
            'common/footer'
        ];

        $this->template = 'chat/chat';
        $this->setOutput();
    }
}