<?php

namespace system;

use system\core\AbstractConstructor;

class Controller extends AbstractConstructor{

    protected $template;
    private $output;
    private $headers = [];
    protected $child = [];
    protected $data = [];
    protected $json = [];
    const CONTROLLER_NAMESPACE = '\\' . LOCATION . '\\controller\\%s\\%s';
    const TEMPLATE_PATH = '%s/../' . LOCATION . '/view/' . (LOCATION == 'public' ? 'default/' : '') . 'template/%s.tpl';

    /**
     * Controller constructor.
     * @param $container
     */
    public function __construct($container){
        parent::__construct($container);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key){
        return $this->container->get($key);
    }

    /**
     * @param $key
     * @param $val
     */
    public function __set($key, $val){
        $this->container->set($key, $val);
    }

    protected function setHeader($header) {
        $this->headers[] = $header;
    }

    /**
     * @param $url
     * @param int $status
     */
    protected function redirect($url, $status = 302){
        header('Status: ' . $status);
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
        exit();
    }

    /**
     * @param $child
     * @param array $args
     * @return mixed
     */
    protected function getChild($child, $args = [])
    {
        list($folder, $class) = explode('/', $child);
        $controller_namespace = sprintf(self::CONTROLLER_NAMESPACE, $folder, ucfirst($class));
        if(class_exists($controller_namespace))
        {
            $controller = new $controller_namespace($this->container);
            $controller->index();
            return $controller->output;
        }else{
            trigger_error('Error: Could not load child ' . $controller_namespace . '!');
            exit();
        }
    }

    /**
     * @return string
     */
    protected function render()
    {
        foreach($this->child as $child)
        {
            $this->data[basename($child)] = $this->getChild($child);
        }
        // domain fix, set default start domain or folder.. //example.com/subfolder/ or //subdomain.example.com/
        $this->data['domain'] = $this->config->get('main', 'domain');
        // domain fix
        $file = sprintf(self::TEMPLATE_PATH, __DIR__, $this->template);
        if (file_exists($file))
        {
            extract($this->data);

            ob_start();

            require $file;

            $this->output = ob_get_contents();

            ob_end_clean();

            if(!empty($this->headers)) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            return $this->output;
        } else {
            trigger_error('Error: Could not load template ' . $file . '!');
            exit();
        }
    }

    /**
     * Output html
     */
    protected function setOutput()
    {
        $this->render();
        echo $this->output;
    }

    /**
     * Output file
     */
    protected function fileOutput($temp_file, $name = 'example')
    {
        if (file_exists($temp_file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $this->setHeader('Content-Description: File Transfer');
            $this->setHeader('Content-Type: application/octet-stream');
            $this->setHeader('Content-Disposition: attachment; filename=' . $name);
            $this->setHeader('Content-Transfer-Encoding: binary');
            $this->setHeader('Expires: 0');
            $this->setHeader('Cache-Control: must-revalidate');
            $this->setHeader('Pragma: public');
            $this->setHeader('Content-Length: ' . filesize($temp_file));

            if (!empty($this->headers)) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }

            if ($f = fopen($temp_file, 'rb')) {
                while (!feof($f)) {
                    print fread($f, 1024);
                }
                fclose($f);
            }

            unlink($temp_file);
        }

        die;
    }

    /**
     * Output json
     */
    protected function jsonOutput()
    {
        echo json_encode($this->json);
    }
}