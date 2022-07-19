<?php

namespace GKTOMK\Controllers;

class Controller {

    public function answerAjax($data){
        exit(json_encode($data));
    }


}