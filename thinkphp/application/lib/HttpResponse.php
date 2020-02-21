<?php

namespace app\lib;

class HttpResponse
{
    public static function show($stateCode,$message = '',$data = []){
        $result = [
            'stateCode' => $stateCode,
            'message' => $message,
            'data' => $data,
        ];
        echo json_encode($result);
    }
}