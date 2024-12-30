<?php

if (!function_exists('product_cate_name')) {
    function product_cate_name($cid)
    {
        $res = ZFTB('product_cate')->where(['status' => 1, 'cid' => $cid])->value('name');
        return $res;
    }
}
if (!function_exists('get_sku_info')) {
    function get_sku_info($data, $gid, $type = 1)
    {
        foreach ($data as $k => $vo) {
            $whereor[] = ['sku_id', '=', $vo];
        }
        $r_parm = ZFTB('product_sku_info_parm')->field('info_id,id,sku_id ,count(id) as sumii')->whereor($whereor)
                                               ->group('info_id')->where(function ($query) use ($gid) {
        })->order("sumii desc,id desc")->find();
        if (isset($r_parm['info_id'])) {
            $info_id = $r_parm['info_id'];
        } else {
            $info_id = false;
        }
        if ($info_id) {
            $res = ZFTB('product_sku_info')->where(['id' => $info_id, 'status' => 1])->find();
            if ($res) {
                return $res;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
