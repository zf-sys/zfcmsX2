<?php
namespace zf;
class ZfAuth{
    public function __construct (){
        // config() = config();
        $this->u_key = config()['zf_auth']['key'];
        $this->s_ver = config()['version']['version'];
        $this->s_soft_id = config()['version']['soft_id'];
        if(is_file('./extend/zf/Yun.php')){
            $this->Yun = new \zf\Yun(2);
            $this->is_professional_edition = true;
        }else{
            $this->is_professional_edition = false;
        }
        
    }
    
    public function __call( $methodName, $arguments){
        if($this->is_professional_edition){
            if(empty($arguments)){
                return $this->Yun->$methodName();
            }else{
                $count =  count($arguments);
                switch ($count){
                    case 1:
                        return $this->Yun->$methodName($arguments[0]);
                        break;
                    case 2:
                        return $this->Yun->$methodName($arguments[0],$arguments[1]);
                        break;
                    case 3:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2]);
                        break;
                    case 4:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3]);
                        break;
                    case 5:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4]);
                        break;
                    case 6:
                        return $this->Yun->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3],$arguments[4],$arguments[5]);
                        break;
                    default:
                        return $this->Yun->$methodName();
                }
            }
        }
    }
    
    
    
    
}