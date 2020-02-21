<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return '';
    }

    public function hello($name = 'ThinkPHP5')
    {
        echo 'hello,' . $name;
    }

    public function test(){
        return "time :".time().PHP_EOL;
    }
}
