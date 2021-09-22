<?php

namespace app\core;

class Request
{
    public function getPath()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getSanitizedData()
    {
        $data= [];

        if($this->getMethod() == 'get')
        {
            foreach($_GET as $key => $value)
            {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if($this->getMethod() == 'post')
        {
            foreach($_POST as $key => $value)
            {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $data;
    }
}