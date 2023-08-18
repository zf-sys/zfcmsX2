<?php
// +----------------------------------------------------------------------
// | 子枫CMS管理系统
// +----------------------------------------------------------------------
// | Copyright (c)  http://store.zf-sys.com/
// | 子枫CMS管理系统提供免费使用,可使用此框架进行二次开发
// +----------------------------------------------------------------------
// | Author: 子枫 <287851074@qq.com>
// | 子枫社区:  http://bbs.90ckm.com/
// +----------------------------------------------------------------------

namespace app\admin\controller;
use think\facade\Request;
use think\Db;

/**
 * echo  '已废弃,请在common/controller/Updatesql.php中使用';
 */
class Updatesql extends Admin
{
    public function __construct (){
        parent::__construct();
        $this->tb_prefix = config()['database']['prefix'];
        $this->Yun = new \zf\Yun();
    }

    public function index(){
        die;
    }
    public function update()
    {
        return true;
    }
    
        


}
