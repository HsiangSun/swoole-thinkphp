<?php


namespace app\index\controller;




use app\lib\HttpResponse;

class Login
{

    public function index(){

        $age = intval($_GET['age']);
        $name = strval($_GET['name']);

        //task 任务
        $_POST['swoole_http']->task(666);




        if ($age == 20 && $name == 'jimmy'){

            return HttpResponse::show(config('code.success'),'login success');

        }else{
            return HttpResponse::show(config('code.error'),'login fail');
        }



    }
}