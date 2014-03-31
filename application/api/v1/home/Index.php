<?php

class Index extends Rest
{

    public function main()
    {
        $message = array(
            'status' => 'SUCCESS',
            'msg' => 'GunsPHP API Working File'
        );
        $this->response(json_encode($message), 200);
    }

} 