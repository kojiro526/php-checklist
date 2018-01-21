<?php
namespace PhpChecklist\Libs\Doc;

use Symfony\Component\Yaml\Yaml;

class Metadata
{

    private $metadata;

    private $title;

    private $subtitle;

    private $author;

    private $date;

    private $toc_title;

    private $abstract;

    public function __construct($text)
    {
        $yaml = Yaml::parse($text);
        $this->metadata = $yaml;
        
        if (empty($yaml))
            return null;
        if (array_key_exists('title', $yaml))
            $this->title = $yaml['title'];
        if (array_key_exists('subtitle', $yaml))
            $this->subtitle = $yaml['subtitle'];
        if (array_key_exists('author', $yaml))
            $this->author = $yaml['author'];
        if (array_key_exists('date', $yaml))
            $this->date = $yaml['date'];
        if (array_key_exists('toc-title', $yaml))
            $this->toc_title = $yaml['toc-title'];
        if (array_key_exists('abstract', $yaml))
            $this->abstract = $yaml['abstract'];
    }

    public function getTitle()
    {
        if (empty($this->title))
            return '';
        return $this->title;
    }

    public function getSubtitle()
    {
        if (empty($this->subtitle))
            return '';
        return $this->subtitle;
    }

    public function getAuthor()
    {
        if (is_string($this->author))
            return $this->author;
        if (is_array($this->author))
            return implode(', ', $this->author);
        return '';
    }

    public function getDate()
    {
        if (empty($this->date))
            return '';
        return $this->date;
    }

    public function getTocTitle()
    {
        if (empty($this->toc_title))
            return '';
        return $this->toc_title;
    }

    public function getAbstract()
    {
        if (empty($this->abstract))
            return '';
        return $this->abstract;
    }

    public function toArray()
    {
        if (empty($this->metadata))
            return [];
        return $this->metadata;
    }
}