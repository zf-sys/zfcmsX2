<?php
namespace app\common\controller;

class Api
{
    public function __construct(){

    }
    public function common_act(){
        do_action('common_act',$this);
    }
}