<?php

namespace system\core\document;

class Document{
    /**
     * @var string $title
     */
    private $title = '';

    /**
     * @var array $styles
     */
    private $styles = [];

    /**
     * @var array $scripts
     */
    private $scripts = [];

    /**
     * @var array $links
     */
    private $links = [];

    /**
     * @var string $keywords
     */
    private $keywords = '';

    /**
     * @var string $description
     */
    private $description = '';

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param $href
     * @param $rel
     * @param $media
     */
    public function addStyle($href, $rel = 'stylesheet', $media = '')
    {
        $this->styles[] = [
            'href' => $href,
            'rel' => $rel,
            'media' => $media
        ];
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * @param array $script
     */
    public function addScript($script)
    {
        $this->scripts[] = $script;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param $href
     * @param $rel
     */
    public function setLinks($href, $rel)
    {
        $this->links[] = [
            'href' => $href,
            'rel' => $rel
        ];
    }
}