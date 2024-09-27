<?php
namespace lib;
/**
 * PDO连接，只需要简单配置数据连接
  使用方法如下


  $db = new \lib\Dbbak('localhost','root','','guestbook','utf8','data/dbbak/');

  //查找数据库内所有数据表
  $tableArry = $db->getTables();

  //备份并生成sql文件
  if(!$db->exportSql($tableArry)){
  echo '备份失败';
  }else{
  echo '备份成功';
  }

  //恢复导入sql文件夹
  if($db->importSql($dir_name)){
  echo '恢复成功';
  }else{
  echo '恢复失败';
  }

 */
class Dbbak {

    public $dbhost; //数据库主机
    public $dbuser; //数据库用户名
    public $dbpw; //数据库密码
    public $dbname; //数据库名称
    public $dataDir; //备份文件存放的路径
    protected $transfer = "";   //临时存放sql[切勿不要对该属性赋值，否则会生成错误的sql语句]

    public function __construct($dbhost, $dbuser, $dbpw, $dbname, $charset = 'utf8', $dir = 'data/dbbak/') {
        $this->connect($dbhost, $dbuser, $dbpw, $dbname, $charset); //连接数据
        //判断$dir最后是否有'/'
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        $this->dataDir = $dir;
    }

    /**
     * 数据库连接
     * @param string $host 数据库主机名
     * @param string $user 用户名
     * @param string $pwd  密码
     * @param string $db   选择数据库名
     * @param string $charset 编码方式
     */
    public function connect($dbhost, $dbuser, $dbpw, $dbname, $charset = 'utf8') {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpw = $dbpw;
        $this->dbname = $dbname;
        try {
            $this->conn = new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpw);
            // 设置 PDO 错误模式为异常
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // 设置字符集
            $this->conn->exec("set names $charset");
            return true;
        } catch(\PDOException $e) {
//            $this->error('无法连接数据库服务器'.$e->getMessage());
            return false;
        }
    }

    /**
     * 列表数据库中的表
     * @param  database $database 要操作的数据库名
     * @return array    $dbArray  所列表的数据库表
     */
    public function getTables($database = '') {
        $database = empty($database) ? $this->dbname : $database;
        $sql = "SHOW TABLES FROM `$database`";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // dd($result);
        //删除Tables_in_master_x2_zfcms_
        foreach ($result as $key => $value) {
            $dbArry[] = $value['Tables_in_'.$database];
        }
        return $dbArry;
    }

    /**
     * 生成sql文件，导出数据库
     * @param string $sql sql    语句
     * @param number $subsection 分卷大小，以KB为单位，为0表示不分卷
     */
    public function exportSql($table = '', $subsection = 0) {
        $table = empty($table) ? $this->getTables() : $table;
        if (!$this->_checkDir($this->dataDir)) {
//            $this->error('您没有权限操作目录,备份失败');
            return false;
        }
        if($subsection == 0){
            if (!is_array($table)) {
                $this->_setSql($table, 0, $this->transfer);
            } else {
                for ($i = 0; $i < count($table); $i++) {
                    // dd($table[$i]);
                    $this->_setSql($table[$i], 0, $this->transfer);
                }
            }
            $fileName = $this->dataDir  . 'all.sql.php';
            if (!$this->_writeSql($fileName, $this->transfer)) {
                return false;
            }
        }else{
            if (!is_array($table)) {
                $sqlArry = $this->_setSql($table, $subsection, $this->transfer);
                $sqlArry[] = $this->transfer;
            } else {
                $sqlArry = array();
                for ($i = 0; $i < count($table); $i++) {
                    $tmpArry = $this->_setSql($table[$i], $subsection, $this->transfer);
                    $sqlArry = array_merge($sqlArry, $tmpArry);
                }
                $sqlArry[] = $this->transfer;
            }
            for ($i = 0; $i < count($sqlArry); $i++) {
                $fileName = $this->dataDir  . 'part' . $i . '.sql.php';
                if (!$this->_writeSql($fileName, $sqlArry[$i])) {
                    return false;
                }
            }
        }
        return true;


    }

    /*
     * 载入sql文件，恢复数据库
     * @param diretory $dir
     * @return booln
     * 注意:请不在目录下面存放其它文件和目录，以节省恢复时间
     */

    public function importSql($dir = '',$type='') {
        $sqls = '';
        if (is_file($dir)) {
            if($type=='get_sql'){
                //获取sql文件内容
                $sqls = file_get_contents($dir);
                return $sqls;
            }
            return $this->_importSqlFile($dir);
        }
        $dir = empty($dir) ? $this->dataDir : $dir;
        if ($link = opendir($dir)) {
            $fileArry = scandir($dir);
            $pattern = "/part[0-9]+.sql.php$|all.sql.php$/";
            $num = count($fileArry);
            for ($i = 0; $i < $num; $i++) {
                if (preg_match($pattern, $fileArry[$i])) {
                    if($type=='get_sql'){
                        $sqls .= file_get_contents($dir .'/'. $fileArry[$i])."\n";
                    }else{
                        if (false == $this->_importSqlFile($dir .'/'. $fileArry[$i])) {
                            return false;
                        }
                    }
                }
            }
            if($type=='get_sql'){
                return $sqls;
            }else{
                return true;
            }
        }
    }

    //执行sql文件，恢复数据库
    protected function _importSqlFile($filename = '') {
        try {
            $sqls = file_get_contents($filename);
            $sqls = substr($sqls, 13);
            $sqls = explode(";\n", $sqls);

            if (empty($sqls)) {
                return false;
            }

            foreach ($sqls as $sql) {
                if (empty($sql)) {
                    continue;
                }
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
            }

            return true;
        } catch (\PDOException $e) {
//            $this->error('恢复失败：' . $e->getMessage());
            return false;
        }
    }
    //执行sql文件，恢复数据库
    public function exec($sql=''){
        if(substr($sql,0,13) == '<?php exit;?>'){
            $sql = substr($sql, 13);
        }
        $sqls = explode(";\n", $sql);
        if (empty($sqls)) {
            return false;
        }
        try{
            foreach ($sqls as $sql) {
                if (empty($sql)) {
                    continue;
                }
                //判断$sql是否值是否只是回车
                if(preg_match('/^\s+$/',$sql)){
                    continue;
                }
                
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
            }
            return true;
        }catch(\PDOException $e){
//            // $this->error('恢复失败：' . $e->getMessage());
            return false;
        }

    }

    /**
     * 执行sql,并返回结果
     * @param $sql
     * @return  返回值
     */
    public function exec_return($sql=''){
        if(substr($sql,0,13) == '<?php exit;?>'){
            $sql = substr($sql, 13);
        }
        $sqls = explode(";\n", $sql);
        if (empty($sqls)) {
            return false;
        }
        $results = array();
        try {
            foreach ($sqls as $sql) {
                if (empty($sql) || preg_match('/^\s+$/', $sql)) {
                    continue;
                }
                
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                
                // 获取查询结果
                if ($stmt->columnCount() > 0) {
                    $results[] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } else {
                    $results[] = $stmt->rowCount();
                }
            }
            return ['code'=>1,'msg'=>'success','data'=>$this->resultToHtml($results,$sqls)];
        } catch(\PDOException $e) {
            // 可以选择记录错误日志
            return ['code'=>0,'msg'=>'error','data'=>$e->getMessage()];
        }

    }
    private function resultToHtml($result,$sqls)
    {
        $html = '';
        
        foreach ($result as $index => $item) {
            $html .= "<h3>结果 " . ($index + 1) . "</h3>";
            $html .= "<p>SQL：" . $sqls[$index] . "</p>";
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            
            if (is_array($item)) {
                if (isset($item[0]) && is_array($item[0])) {
                    // 处理二维数组
                    $html .= '<tr><th>' . implode('</th><th>', array_keys($item[0])) . '</th></tr>';
                    foreach ($item as $row) {
                        $html .= '<tr>';
                        foreach ($row as $value) {
                            $html .= '<td>' . $this->formatValue($value) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                } else {
                    // 处理一维数组
                    $html .= '<tr><th>键</th><th>值</th></tr>';
                    foreach ($item as $key => $value) {
                        $html .= "<tr><td>{$key}</td><td>" . $this->formatValue($value) . "</td></tr>";
                    }
                }
            } else {
                // 处理非数组结果
                $html .= "<tr><td>结果</td><td>" . $this->formatValue($item) . "</td></tr>";
            }
            
            $html .= '</table><br>';
        }
        
        return $html;
    }
    private function formatValue($value)
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_object($value)) {
            return json_encode((array)$value, JSON_UNESCAPED_UNICODE);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'NULL';
        } else {
            return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * 生成sql语句
     * @param   $table     要备份的表
     * @return  $tabledump 生成的sql语句
     */
    protected function _setSql($table, $subsection = 0, &$tableDom = '') {
        $tableDom .= "DROP TABLE IF EXISTS $table;\n";
    
        // 获取表结构
        $stmt = $this->conn->prepare("SHOW CREATE TABLE $table");
        $stmt->execute();
        $create = $stmt->fetch(\PDO::FETCH_NUM);
        $create[1] = str_replace("\n", "", $create[1]);
        $create[1] = str_replace("\t", "", $create[1]);
    
        $tableDom .= $create[1] . ";\n";
    
        // 获取表数据
        $stmt = $this->conn->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(\PDO::FETCH_NUM);
        try{
            $numfields = count($rows[0]);
        }catch(\Exception $e){
            // echo '表'.$table.'没有数据';
            $numfields = 0;
        }
        $numrows = count($rows);
        $n = 1;
        $sqlArry = array();
    
        foreach ($rows as $row) {
            $comma = "";
            $tableDom .= "INSERT INTO $table VALUES(";
            for ($i = 0; $i < $numfields; $i++) {
                $tableDom .= $comma . "" . $this->conn->quote($row[$i]) . "";
                // $tableDom .= $comma . "'" . $this->conn->quote($row[$i]) . "'";
                $comma = ",";
            }
            $tableDom .= ");\n";
    
            if ($subsection != 0 && strlen($this->transfer) >= $subsection * 1000) {
                $sqlArry[$n] = $tableDom;
                $tableDom = '';
                $n++;
            }
        }
    
        return $sqlArry;
    }

    /**
     * 验证目录是否有效，同时删除该目录下的所有文件
     * @param diretory $dir
     * @return booln
     */
    protected function _checkDir($dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777);
        }
        if (is_dir($dir)) {
            if ($link = opendir($dir)) {
                $fileArry = scandir($dir);
                for ($i = 0; $i < count($fileArry); $i++) {
                    if ($fileArry[$i] != '.' || $fileArry[$i] != '..') {
                        @unlink($dir . $fileArry[$i]);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 将数据写入到文件中
     * @param file $fileName 文件名
     * @param string $str   要写入的信息
     * @return booln 写入成功则返回true,否则false
     */
    protected function _writeSql($fileName, $str) {
        $re = true;
        if (!$fp = @fopen($fileName, "w+")) {
            $re = false;
//            $this->error("在打开文件时遇到错误，备份失败!");
        }
        if (!@fwrite($fp, '<?php exit;?>' . $str)) {
            $re = false;
//            $this->error("在写入信息时遇到错误，备份失败!");
        }
        if (!@fclose($fp)) {
            $re = false;
//            $this->error("在关闭文件 时遇到错误，备份失败!");
        }
        return $re;
    }

    public function error($str) {
        throw new \Exception($str);
    }

}

?>