<?php

namespace App\Traits;


trait EscapeFileUrlTrait {

    public function escapefile_url($url){
        $parts = parse_url($url);
        $path_parts = array_map('rawurldecode', explode('/', $parts['path']));

        return
            $parts['scheme'] . '://' .
            $parts['host'] .
            implode('/', array_map('rawurlencode', $path_parts)) .
            (isset($parts['query']) ? '?'.rawurldecode($parts['query']) : '')
        ;
    }

}