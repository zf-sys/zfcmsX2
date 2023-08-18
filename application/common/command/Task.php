<?php
namespace app\common\command;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
class Task extends Command
{
    // php think task start
    // php think task status
    // php think task stop
    protected function configure()
    {
        //设置名称为task
        $this->setName('task')
            //增加一个命令参数
            ->addArgument('action', Argument::OPTIONAL, "action")
            ->addArgument('force', Argument::OPTIONAL, "force");
    }
    protected function execute(Input $input, Output $output)
    {
        //获取输入参数
        $action = trim($input->getArgument('action'));
        $force = trim($input->getArgument('force'));
        if(in_array($action,['start','status','stop'])){
        	$_obj = new \think\db\Query();
        	 $_is_hook = method_exists($_obj,'close');
        	 if(!$_is_hook){
        	 	 $output->writeln("\033[38;5;1m ------------Query类的close方法不存在,请打开下面链接修改------------ \033[0m");
        	 	 $output->writeln("\033[38;7;1m -------------- http://bbs.90ckm.com/bbs_detail/138.html -------------- \033[0m");
        	 	 $output->writeln("\033[38;5;1m ---------------------------------ZF-SYS--------------------------------- \033[0m");
        	 	 return '';
        	 }
        }
        // 配置任务
        $task = new \EasyTask\Task();
        // 设置项目名称 /runtime/ThinkTask
        // $task->setPrefix('ThinkTask');
        // 设置系统时区
        $task->setTimeZone('Asia/Shanghai');
        // 设置子进程挂掉自动重启
        $task->setAutoRecover(true);
        // 设置PHP运行路径,一般Window系统才需要设置,当系统无法找到才需要您手动设置
        // ->setPhpPath('C:/phpEnv/php/php-7.0/php.exe');

        /**
         * 设置接收运行中的错误或者异常(方式1)
         * 您可以自定义处理异常信息,例如将它们发送到您的邮件中,短信中,作为预警处理
         * (不推荐的写法,除非您的代码健壮)
         */
        $task->setErrorRegisterNotify(function ($ex) {
            //获取错误信息|错误行|错误文件
            $message = $ex->getMessage();
            $file = $ex->getFile();
            $line = $ex->getLine();
            logOutput("定时任务异常:错误信息:".$message."|相关文件路径:".$file."|行数:".$line."|",'task_error.log');

        });
        /**
         * 设置接收运行中的错误或者异常的Http地址(方式2)
         * Easy_Task会POST通知这个url并传递以下参数:
         * errStr:错误信息
         * errFile:错误文件
         * errLine:错误行
         * 您的Url收到POST请求可以编写代码发送邮件或短信通知您
         * (推荐的写法)
         */
        // $task->setErrorRegisterNotify('https://www.gaojiufeng.cn/rev.php');

        $task->setRunTimePath('./runtime/');
        $task->setDaemon(true); //设为守护进程
        
        $task->addFunc(function () {
            if(!is_file('./public/task_lock.txt')){
                file_put_contents('./public/task_lock.txt','0');
            }
            try {
                $this->monitor();
            } catch (Exception $e) {
                $msg = $e->getMessage();
                logOutput('定时任务错误:'.$msg,'task_error.log');
            }
        }, 'command_tp', 1, 1);
        //半个小时执行一次重启
        $command_tp_restart = 'php think task start';
        $task->addCommand($command_tp_restart,'command_tp_restart',60*30,1);
    
        // 根据命令执行 每隔1s执行一次monitor函数，只开启一个进程
        if ($action == 'start'){
            file_put_contents('./public/task_lock.txt',time());
            $task->start();
        }elseif ($action == 'status'){
            $task->status();
        }elseif ($action == 'stop'){
            file_put_contents('./public/task_lock.txt',0);
            $force = ($force == 'force'); //是否强制停止
            $task->stop($force);
        }else{
            exit('Command is not exist');
        }
        return;
    }
    protected function monitor()
    {
        // $Base = new \app\common\controller\Base(false);
        // $Base->task_notice();
        $m = date('s');
        if($m=='30'){
            logOutput('执行中,当前时间:'.date('Y-m-d H:i:s',time()),'task_error.log');
        }
        Db::startTrans();
        try {
            $where[] = ['status','=',1];
            $where[] = ['stime','<',time()];
            $where[] = ['etime','>',time()];
            $list = Db::name('task')->where($where)->order('sort desc,id desc')->select();
            // logOutput('执行中,执行数量:'.count($list),'task_error.log');
            foreach($list as $k=>$vo){
                $is = Db::name('task_log')->where([['task_id','=',$vo['id']],['status','=',1]])->order('ctime desc')->find();
                $hour = date('H');
                if(!$is  || ($is['ctime']+$vo['interval_time'])<=time() && in_array($hour,explode(',',$vo['hours'])) ){
                    if($vo['type']=='url'){
                        $ret_data = file_get_contents($vo['data']);
                    }elseif($vo['type']=='class'){
                        $_arr = explode('@',$vo['data']);
                        $controller_name =$_arr[0]; 
                        $action_name =$_arr[1]; 
                        //判断类是否存在
                        if(!class_exists($controller_name)){
                            Db::name('task')->where(['id'=>$vo['id']])->order('sort desc,id desc')->update(['status'=>0]);
                            $ret_data = 'class类不存在,已停止执行';
                        }else{
                            $class = new $controller_name;
                            //判断方法是否存在
                            if(!method_exists($class,$action_name)){
                                Db::name('task')->where(['id'=>$vo['id']])->order('sort desc,id desc')->update(['status'=>0]);
                                $ret_data = 'class方法不存在,已停止执行';
                            }else{
                                $ret_data = $class->$action_name();
                            }
                        }
                        
                    }else{
                        $ret_data = '';
                    }
                    $this->save_log($vo,$ret_data);
                }
            }
            Db::commit();   
            Db::close();
        } catch (\Exception $e) {
            Db::rollback();
            Db::close();
            $r = $e->getMessage();
            logOutput('定时任务,执行monitor错误 :'.$r,'task_error.log');
        }
    }
    protected function save_log($task_data,$ret_data=null){
        $data['ctime'] = time();
        $data['task_id'] = $task_data['id'];
        $data['task_data'] = json_encode($task_data);
        $data['ret_data'] = json_encode($ret_data);
        $data['status'] = 1;
        Db::name('task_log')->insert($data);
    }
}