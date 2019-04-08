<?php
namespace PhpChecklist\Libs\Doc;

class Tags
{

    private $raw = '';

    private $tags = null;

    public function __construct($text)
    {
        $this->raw = trim($text);
    }

    public function toArray()
    {
        if (is_array($tags))
            return $this->tags;
        
        if (! is_string($this->raw) || $this->raw === '') {
            $this->tags = [];
            return $this->tags;
        }
        
        $tags = preg_split('/[\s]+/', $this->raw);
        if ($tags === false) {
            throw new \Exception(sprintf('Invalid format tags: %', $this->raw));
        }
        $this->tags = array_values(array_unique($tags));
        return $this->tags;
    }

    public function has($search)
    {
        if (is_null($search)) return false;
        if ($search == '') return false;

        $filtered = array_filter($this->toArray(), function ($value) use ($search) {
            return (trim($search) === $value);
        });
        
        $filtered = array_values($filtered);
        return (count($filtered) > 0);
    }
}