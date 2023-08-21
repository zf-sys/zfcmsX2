<?php
namespace zf;


class FileDownload {
    /**
     * 下载远程大文件
     *
     * @param string $sourceFileUrl  远程文件地址
     * @param string $targetFile     远程文件下载到本地的本地地址
     * @param float|int $pieceSize   每次分片大小 M (默认100 因采用命令形式，不占用程序执行时内存)
     * @return bool
     */
    public static function downloadLargeFile($sourceFileUrl, $targetFile, $pieceSize = 100,$tag='') {
        $pieceSize = 1024 * 1024 * $pieceSize;
        $size = self::getRemoteFileSize($sourceFileUrl);
        if ($size === false) {
            return false;
        }
        $from = 0;
        $i = 0;
        $isSuccess = true;
        do {
            $to = min($from + $pieceSize - 1, $size);
            $partTargetFile = $targetFile . '_'.$tag.'_' . $i++;
            $partFiles[] = $partTargetFile;
            $r = self::downFileByPiece($sourceFileUrl, $partTargetFile, $from, $to);
            if ($r === false) {
                $isSuccess = false;
                break;
            }
            $from = $to + 1;
            if ($from > $size) {
                break;
            }
        } while(true);

        // 合并文件
        if ($isSuccess) {
            $combineCmd = "cat " . implode(' ', $partFiles) . " > " . $targetFile;
            exec($combineCmd, $o, $ret);
            // dd($ret);
            if ($ret != 0) {
                $isSuccess = false;
            }
        }
        // 清理临时文件
        foreach ($partFiles as $partFile) {
            if (file_exists($partFile)) {
                @unlink($partFile);
            }
        }
        return $isSuccess;
    }

    /**
     * 分片下载远程文件
     *
     * @param string  $sourceFile  远程文件地址
     * @param string  $savePath    远程文件分片下载到本地的本地分片地址
     * @param int $fromByte  远程文件分片起始位置
     * @param int $toByte    远程文件分片结束位置(-1 表示直到文件结束)
     * @return bool
     */
    private static function downFileByPiece($sourceFile, $savePath, $fromByte = 0, $toByte = -1) {
        if (file_exists($savePath)) {
            return true;
        }
        $cmd = "curl --range " . $fromByte . "-";
        if ($toByte > 0) {
            $cmd .= $toByte;
        }
        $cmd .= " -o " . $savePath . " '" . $sourceFile . "'";
        exec($cmd, $o, $ret);
        if ($ret != 0) {
            return false;
        }
        return true;
    }

    /**
     * 获取远程文件的大小
     *
     * @param string $fileUrl  远程文件地址
     * @return false|mixed
     */
    public static function getRemoteFileSize($fileUrl) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$fileUrl);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //忽略https
        if(strpos($fileUrl,'https')!==false){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $head = curl_exec($ch);
        curl_close($ch);
        $regex = '/Content-Length:\s([0-9].+?)\s/';
        preg_match($regex, $head, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }else{
            //获取文件大小

        }
        return false;
    }
}