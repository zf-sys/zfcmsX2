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
use \think\Controller;
use think\Db;
use Env;

class Install extends Controller
{
    public function index($step = 0)
    {
       
        if (is_file('./public/install.lock')) {
            return $this->error('如需重新安装，请手动删除public/install.lock文件');
        }

        switch ($step) {
            case 2:
                session('install_error', false);
                return self::step2();
                break;
            case 3:
                if (session('install_error')) {
                    return $this->error('环境检测未通过，不能进行下一步操作！');
                }
                return self::step3();
                break;
            case 4:
                if (session('install_error')) {
                    return $this->error('环境检测未通过，不能进行下一步操作！');
                }
                return self::step4();
                break;
            case 5:
                if (session('install_error')) {
                    return $this->error('初始失败！');
                }
                return self::step5();
                break;
            
            default:
                session('install_error', false);
                return view('index');
                // return view('index/upload');
                break;
        }
    }

    /**
     * 第二步：环境检测
     * @return mixed
     */
    private function step2()
    {
        $data = [];
        $data['env'] = self::checkNnv();
        $data['dir'] = self::checkDir();
        // echo "<pre>";
        // var_dump($data);die;
        $data['func'] = self::checkFunc();
        $this->assign('data', $data);
        return view('step2');
    }
    
    /**
     * 第三步：初始化配置
     * @return mixed
     */
    private function step3()
    {
        return $this->fetch('step3');
    }
    
    /**
     * 第四步：执行安装
     * @return mixed
     */
    private function step4()
    {
        if ($this->request->isPost()) {
            if (!is_writable('./config/database.php')) {
                return $this->error('[config/database.php]无读写权限！');
            }
            $data = input('post.');
            $data['type'] = 'mysql';
            $rule = [
                'hostname|服务器地址' => 'require',
                'hostport|数据库端口' => 'require|number',
                'database|数据库名称' => 'require',
                'username|数据库账号' => 'require',
                'prefix|数据库前缀' => 'require|regex:^[a-z0-9]{1,20}[_]{1}',
                'cover|覆盖数据库' => 'require|in:0,1',
            ];
            $validate = $this->validate($data, $rule);
            if (true !== $validate) {
                return $this->error($validate);
            }
            $cover = $data['cover'];
            unset($data['cover']);
            $config = include './config/database.php';
            foreach ($data as $k => $v) {
                if (array_key_exists($k, $config) === false) {
                    return $this->error('参数'.$k.'不存在！');
                }
            }
            // 不存在的数据库会导致连接失败
            $database = $data['database'];
            unset($data['database']);
            // 创建数据库连接
            $db_connect = Db::connect($data);
            // 检测数据库连接
            try{
                $db_connect->execute('select version()');
            }catch(\Exception $e){
                $this->error('数据库连接失败，请检查数据库配置！');
            }

            // 不覆盖检测是否已存在数据库
            if (!$cover) {
                $check = $db_connect->execute('SELECT * FROM information_schema.schemata WHERE schema_name="'.$database.'"');
                if ($check) {
                    $this->error('该数据库已存在，如需覆盖，请选择覆盖数据库！');
                }
            }
            // 创建数据库
            if (!$db_connect->execute("CREATE DATABASE IF NOT EXISTS `{$database}` DEFAULT CHARACTER SET utf8")) {
                return $this->error($db_connect->getError());
            }
            $data['database'] = $database;
            // 生成配置文件
            self::mkDatabase($data);
            return $this->success('数据库连接成功', '');
        } else {
            return $this->error('非法访问');
        }
    }
    
    /**
     * 第五步：数据库安装
     * @return mixed
     */
    private function step5()
    {
        //重置站点token
        $web_config = ['site_token'=>time().mt_rand(1,99999999),'site_path'=>'','site_admin_token'=>time().mt_rand(100000,99999999)];
        extraconfig($web_config,'web');
        
        $account = input('post.account');//用户名
        $password = input('post.password');//密码

        $config = include './config/database.php';
        if (empty($config['hostname']) || empty($config['database']) || empty($config['username'])) {
            return $this->error('请先点击测试数据库连接！');
        }
        if (empty($account) || empty($password)) {
            return $this->error('请填写管理账号和密码！');
        }
        $rule = [
            'account|管理员账号' => 'require|alphaNum',
            'password|管理员密码' => 'require|length:6,20',
        ];
        $validate = $this->validate(['account' => $account, 'password' => $password], $rule);
        if (true !== $validate) {
            return $this->error($validate);
        }
        // 导入系统初始数据库结构
        // 导入SQL
        $sql_file = './public/import.sql';
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            $sql_list = parse_sql($sql, 0, ['zf_' => $config['prefix']]);
            if ($sql_list) {
                $sql_list = array_filter($sql_list);
                foreach ($sql_list as $v) {
                    try {
                        Db::execute($v);
                    } catch(\Exception $e) {
                        return $this->error($e->getMessage());
                    }
                }
            }
        }
        // 注册管理员账号
        $map['gid'] = 1;
        $map['name'] = $account;
        $map['ctime'] = time();
        $map['pwd'] = md5('zfcms-'.$password);
        //
        $is_admin = Db::name('admin')->where(['name'=>$map['name']])->find();
        if($is_admin){
            if($is_admin['pwd']==$map['pwd']){
                $res = true;
            }else{
                $res = Db::name('admin')->where(['name'=>$map['name']])->update(['pwd'=>$map['pwd']]);
            }
        }else{
            $res = Db::name('admin')->insert($map);
        }
        if (!$res) {
            return $this->error($user->getError() ? $user->getError() : '管理员账号设置失败！');
        }
        extraconfig(['sql_version'=>'v0.0.0.1'],'version');
        extraconfig(['site_admin_token'=>time(),'site_token'=>md5(time())],'web');
        file_put_contents('./public/install.lock', "如需重新安装，请手动删除此文件\n安装时间：".date('Y-m-d H:i:s'));
        $hs_auth = <<<INFO
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
// | 秘钥请根据链接进行申请 http://bbs.90ckm.com/
// +----------------------------------------------------------------------
return ['email' => '', 'key'=>'', 'sc'=>'' ];
INFO;
        file_put_contents('./config/zf_auth.php', $hs_auth);
        // 获取站点根目录
        $root_dir = request()->baseFile();
        $root_dir  = preg_replace(['/index.php$/'], [''], $root_dir);
        return $this->success('系统安装成功，欢迎您使用', $root_dir.'admin');
    }
    
    /**
     * 环境检测
     * @return array
     */
    private function checkNnv()
    {
        $items = [
            'os'      => ['操作系统', '不限制', '类Unix', PHP_OS, 'ok'],
            'php'     => ['PHP版本', '7.2', '7.2及以上', PHP_VERSION, 'ok'],
            'gd'      => ['GD库', '2.0', '2.0及以上', '未知', 'ok'],
        ];
        if ($items['php'][3] < $items['php'][1]) {
            $items['php'][4] = 'no';
            session('install_error', true);
        }
        $tmp = function_exists('gd_info') ? gd_info() : [];
        if (empty($tmp['GD Version'])) {
            $items['gd'][3] = '未安装';
            $items['gd'][4] = 'no';
            session('install_error', true);
        } else {
            $items['gd'][3] = $tmp['GD Version'];
        }

        return $items;
    }
    
    /**
     * 目录权限检查
     * @return array
     */
    private function checkDir()
    {
        $items = [
            ['dir', './application', '读写', '读写', 'ok'],
            ['dir', './extend', '读写', '读写', 'ok'],
            ['dir', './upload', '读写', '读写', 'ok'],
            ['dir', './public/static', '读写', '读写', 'ok'],
            ['dir', './runtime', '读写', '读写', 'ok'],
            ['file', './config/database.php', '读写', '读写', 'ok'],
        ];
        foreach ($items as &$v) {
            if ($v[0] == 'dir') {
                if(!is_writable($v[1])) {
                    if(is_dir($v[1])) {
                        $v[3] = '不可写';
                        $v[4] = 'no';
                    } else {
                        $v[3] = '不存在';
                        $v[4] = 'no';
                    }
                    session('install_error', true);
                }
            } else {
                if(!is_writable($v[1])) {
                    $v[3] = '不可写';
                    $v[4] = 'no';
                    session('install_error', true);
                }
            }
        }
        return $items;
    }
    
    /**
     * 函数及扩展检查
     * @return array
     */
    private function checkFunc()
    {
        $items = [
            ['pdo', '支持', 'yes', '类'],
            ['pdo_mysql', '支持', 'yes', '模块'],
            ['zip', '支持', 'yes', '模块'],
            ['fileinfo', '支持', 'yes', '模块'],
            ['curl', '支持', 'yes', '模块'],
            ['xml', '支持', 'yes', '函数'],
            ['file_get_contents', '支持', 'yes', '函数'],
        ];

        foreach ($items as &$v) {
            if(('类'==$v[3] && !class_exists($v[0])) || ('模块'==$v[3] && !extension_loaded($v[0])) || ('函数'==$v[3] && !function_exists($v[0])) ) {
                $v[1] = '不支持';
                $v[2] = 'no';
                session('install_error', true);
            }
        }

        return $items;
    }
    
    /**
     * 生成数据库配置文件
     * @return array
     */
    private function mkDatabase(array $data)
    {
        $code = <<<INFO
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

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$data['hostname']}',
    // 数据库名
    'database'        => '{$data['database']}',
    // 用户名
    'username'        => '{$data['username']}',
    // 密码
    'password'        => '{$data['password']}',
    // 端口
    'hostport'        => '{$data['hostport']}',
    // 连接dsn
    'dsn'             => '',
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '{$data['prefix']}',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\\think\\db\\Query',
];
INFO;
        $is_add =  file_put_contents('./config/database.php', $code);
        // 判断写入是否成功
        if(!$is_add){
            return $this->error('[condig/database.php]数据库配置写入失败！');
            exit;
        }
    }
}
