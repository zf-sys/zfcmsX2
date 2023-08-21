<?php
namespace zf;
class ZActObj{
    public function __construct ($data_ini){
        $this->data = $data_ini;
    }
    public function push_arr($data){
        array_push($this->data,$data);
    }
    public function update_str($data){
        $this->data = $data;
    }
   
}
