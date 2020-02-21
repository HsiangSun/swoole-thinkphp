<?php

// namespace Http\Main;

use app\lib\AliSmc;
//use Swoole\Http\Server;
use think\Container;
use Swoole\WebSocket\Server;


class Http
{

    public $http = null;
    const HOST = '0.0.0.0';
    const PORT = 8080;
    public function __construct()
    {
        $this->http = new Server(self::HOST, self::PORT);
        $this->http->set([
        'enable_static_handler'=>true,
        'document_root'=>"/home/hoho/IdeaProjects/live/thinkphp/public/static",
        'worker_num' => 6,
        'task_worker_num' => 4,
        ]);

        //顺序不对都要报错有点坑
        $this->http->on('workerStart',[$this,'workerStart']);
        $this->http->on('task',[$this,'handleTask']);
        $this->http->on('request',[$this,'handleRequest']);
        $this->http->on('finish',[$this,'handleFinished']);
        $this->http->on('open',[$this,'handleOpen']);
        $this->http->on('message',[$this,'handleMessage']);
        $this->http->on('close',[$this,'handleClose']);
        $this->http->start();
    }

    public function workerStart(){
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        //require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../thinkphp/start.php';//加载整个tp但是会执行index/index/index方法
    }

    public function handleRequest($req,$rep){


        /*if ($req->server['path_info'] == '/favicon.ico' || $req->server['request_uri'] == '/favicon.ico') {
            return $rep->end();
        }*/

        $_SERVER = [];
        if (isset($req->server)){
            foreach ($req->server as $k => $v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        if (isset($req->header)){
            foreach ($req->header as $k => $v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }

        //$_GET超全局变量不会自动释放需要手动判断并释放或者直接close当前http
//        if (!empty($_GET)){
//            unset($_GET);
//        }
        $_GET = [];
        if (isset($req->get)){
            foreach ($req->get as $k => $v){
                $_GET[$k] = $v;
            }
        }

        $_POST = [];
        if (isset($req->post)){
            foreach ($req->post as $k => $v){
                $_POST[$k] = $v;
            }
        }

        //将http对象传到tp框架的任何地方
        $_POST['swoole_http'] = $this->http;

        //开启缓冲
        ob_start();
        try{
            // 执行应用并响应
            Container::get('app', [APP_PATH])
                ->run()
                ->send();
        }catch (Exception $e){
            /*if ($e->getCode() == 0){//method not exists
                $rep->status(404,"404 NOT FOUND");
            }*/
            echo $e->getMessage().PHP_EOL;
        }

        $buffContent = ob_get_contents();
        //关闭缓冲
        ob_end_clean();
        $rep->end($buffContent);

    }

    public function handleTask($serv, $task_id, $from_id, $data){
        $code = $data;
        $obj = new AliSmc();
        $obj->hello($code);
        return "task finished";
    }

    public function handleFinished($serv, $task_id, $data){
        echo "AsyncTask[$task_id] Finished: $data".PHP_EOL;
    }

    public function handleOpen($ws,$req){
        var_dump($req->fd, $req->get, $req->server);
        $ws->push($req->fd, "hello, welcome\n");
    }

    public function handleMessage($ws,$frame){
        echo "Message: {$frame->data}\n";
        $ws->push($frame->fd, "server: {$frame->data}");
    }

    public function handleClose($ws, $fd){
        echo "client-{$fd} has closed\n";
    }


}

new Http();







