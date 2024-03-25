<?php
// WebSite: http://www.zf-sys.com/
// Document:  http://bbs.90ckm.com/
// Bbs:  http://bbs.90ckm.com/
// Github: https://github.com/zf-sys/zfcmsX2
// Gitee:https://gitee.com/zf-sys/zfcmsX2
// Feedback: https://support.qq.com/products/166743
namespace app\common\handle;
use think\Controller;
use think\Db;
use GuzzleHttp\Client;
use think\facade\Request;
class OrderPay extends Controller
{
    public function __construct (){
        parent::__construct();
    }
    public function orderPayHandle($data){
        // dd($data);
        // array(6) {
        //     ["order_sn"]=>
        //     int(1212121)
        //     ["trans_id"]=>
        //     string(7) "1234567"
        //     ["currency"]=>
        //     string(3) "CNY"
        //     ["pay_type"]=>
        //     string(10) "EpayAlipay"
        //     ["amount_in"]=>
        //     float(0.1)
        //     ["paid_time"]=>
        //     string(19) "2024-03-25 14:40:37"
        //   }
        try{
            Db::startTrans();
            $order = Db::name('order')->where('order_sn',$data['order_sn'])->find();
            if($order){
                if($order['money']!= $data['amount_in']){
                    Db::rollback();
                    @save_exception($type='pay_callback','金额不一致',$data=['req_data'=>$data],$code=0);
                    return jsonPro($data,$msg='金额不一致',$code=0);
                }
                if($order['pay_status'] == 1){
                    Db::rollback();
                    return jsonPro($data,$msg='订单已支付,请勿重复',$code=0);
                }
                // dd($order);
                // 判断支付方式
                if($order['pay_type'] != $data['pay_type']){
                    Db::rollback();
                    @save_exception($type='pay_callback','支付方式不一致',$data=['req_data'=>$data],$code=0);
                    return jsonPro($data,$msg='支付方式不一致',$code=0);
                }
                // 修改订单状态
                $is_update = Db::name('order')->where([['order_sn','=',$data['order_sn']]])->update([
                    'pay_status' => 1,
                    'prepay_id'=>$data['trans_id'], //交易号
                    'pay_time'=>strtotime($data['paid_time']),
                ]);
                // dd($data['order_sn']);
                if(!$is_update){
                    Db::rollback();
                    @save_exception($type='pay_callback','更新状态失败1',$data=['req_data'=>$data],$code=0);
                    return jsonPro($data,$msg='更新状态失败1',$code=0);
                }
                Db::commit();
                return jsonPro($data=['req_data'=>$data],$msg='支付成功',$code=1);
            }else{
                Db::rollback();
                @save_exception($type='pay_callback','订单不存在',$data=['req_data'=>$data],$code=0);
                return jsonPro($data,$msg='订单不存在',$code=0);
            }

        }catch(\Exception $e){
            Db::rollback();
            //记录支付异常
            @save_exception($type='pay_callback',$e->getMessage(),$data=['req_data'=>$data],$code=0);
            return $this->error($e->getMessage());
        }
    }

    
    
}

