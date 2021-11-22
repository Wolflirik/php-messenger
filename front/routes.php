<?php

$this->router->add('chat',                 '/',           'common\Chat:index');
$this->router->add('auth_register',        '/register',   'common\Auth:register');
$this->router->add('auth_register_submit', '/register',   'common\Auth:register', 'POST');
$this->router->add('auth_login',           '/login',      'common\Auth:login');
$this->router->add('auth_login_submit',    '/login',      'common\Auth:login', 'POST');
$this->router->add('auth_logout',          '/logout',     'common\Auth:logout');

$this->router->add('user_update',          '/user/update',     'user\User:index');
$this->router->add('user_update_submit',   '/user/update',     'user\User:index', 'POST');
$this->router->add('search_user',          '/search',          'search\Search:index');
