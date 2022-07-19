<?php


namespace GKTOMK\Models;


class Events
{
    public $request;
    public function __construct($request)
    {
        $this->request = $request;
    }
}