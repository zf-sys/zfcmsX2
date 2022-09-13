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
namespace app\index\controller;
use think\Db;
use think\facade\Request;
use think\facade\Hook;
/**
 * @title 测试接口
 * @controller app\index\controller\Ems
 * @group base
 */
class Test extends Base
{
    /**
     * @title 发送验证码 init
     * @desc 最基础的接口注释写法
     * @author zf
     * @method GET
     * @tag 邮箱 验证码
     * @param name:email type:string require:1 desc:邮箱
     * @param name:event type:string  desc:事件名称
     * @return name:data type:array ref:definitions\dictionary
     */
	public function __construct (){
        parent::__construct();

        
    }
    
    /**
     * @title 发送验证码
     * @desc 最基础的接口注释写法
     * @author zf
     * @url /index/test/send
     * @method GET
     * @tag 邮箱 验证码
     * @param name:email type:string require:1 desc:邮箱
     * @param name:event type:string  desc:事件名称
     * @return name:data type:array ref:definitions\dictionary
     */
    public function send()
    {
       return jserror(1);
    }
   
   
}


