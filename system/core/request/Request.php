<?php

namespace system\core\request;

use system\helper\Cookie;

class Request{
    /**
     * @var array $get
     */
    public $get;

    /**
     * @var array $post
     */
    public $post;

    /**
     * @var array $request
     */
    public $request;

    /**
     * @var array $server
     */
    public $server;

    /**
     * @var array $cookie
     */
    public $cookie;

    /**
     * @var array $files
     */
    public $files;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
    }
}