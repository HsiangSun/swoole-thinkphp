<?php


namespace app\lib;

/*
 *
 * 模拟Task任务
 * */
class AliSmc
{
    public function hello($code){
        sleep(10);
        echo "Hello from Alismc code =".$code.PHP_EOL;
    }
}