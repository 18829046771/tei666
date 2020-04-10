<?php
namespace Wan\Controller;
use Think\Controller;

class BmController extends PublicController {
	
    public function _initialize(){	
        $user = $_SESSION['user'];
        if(!isset($user) || empty($user)){
            $this->weixindeng();
        }
    }

    public function order(){
        $hid = I('hid/d');
        empty($hid) && die("<script>alert('参数错误！');history.go(-1); </script>");

        $hd = M('huodong')->where(['id' => $hid])->find();

        $url = U('Index/index');
        if ($hd) {
            $user = M("user")->where(['id' => $_SESSION['user']['id']])->find();
            if ($user['type'] == 1) {
                $hd['money'] = $hd['money_hy'];
            }

            if ($hd['end_time'] < time()) {
                die("<script>alert('该活动已结束！');history.go(-1); </script>");
            }

            if ($hd['type'] == 1 || $hd['type'] == 3) {
                if ($user['type'] != 1) {
                    die("<script>alert('该活动仅限会员参与！');history.go(-1); </script>");
                }
            }

            $bm = M('baoming')->where(['uid' => $user['id'], 'hid' => $hid])->find();
            if ($bm) {
                // if ($bm['pay_state'] == 1) {
                //     die("<script>alert('您已参与该活动！');history.go(-1); </script>");
                // }
                if ($bm['pay_state'] == 0) {
                    die("<script>alert('您已参与该活动，订单未支付，请前往我报名的活动进行支付！');window.location.href='" . U('My/play') . "'; </script>");
                }
            }

            $count = M('baoming')->where(['hid' => $hd['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
            $count = $count ? $count : 0;
            $hd['sy'] = $hd['num'] - $count; // 剩余名额
            $this->hd = $hd;
            $this->user = $user;
            $this->display();
        } else {
            die("<script>alert('活动信息不存在！');history.go(-1); </script>");
        }
    }

    // 支付
    public function pay(){
        if (IS_GET) {
            $data = I('get.');

            $num = I('get.num/d');
            empty($num) && die('请选择数量！');
            $hid = I('get.hid/d');
            empty($hid) && die('活动参数缺失！');
            $sename = I('get.sename');
            empty($sename) && die('请输入真实姓名！');
            $photo = I('get.photo');
            empty($photo) && die('请输入手机号码！');

            $db = M("baoming");
            $hd = M('huodong')->where(['id' => $hid])->find();
            $user = M("user")->where(['id' => $_SESSION['user']['id']])->find();

            if ($hd) {

                // 修改用户手机号码和姓名信息
                // M('user')->where(['id' => $_SESSION['user']['id']])->save([
                //     'sename' => $sename,
                //     'photo' => $photo
                // ]);

                $count = M('baoming')->where(['hid' => $hd['id'], 'pay_state' => 1, 'tuikuan' => 0])->sum('number');
                $count = $count ? $count : 0;
                $sy = $hd['num'] - $count; // 剩余名额
                if ($sy <= 0) {
                    die("<script>alert('该活动已售罄！');history.go(-1);</script>");
                }

                $add['uid'] = $user['id'];
                $add['hid'] = $hid;
                $add['title'] = $hd['title'];
                $add['type'] = $hd['type'];
                $add['paysn'] = makePaySn($hid);
                $add['number'] = $num;

                if ($user['type'] == 1) {
                    $money = $hd['money_hy'];
                } else{
                    $money = $hd['money'];
                }

                $add['money'] = $money * $num;
                $add['name'] = $sename;
                $add['phone'] = $photo;
                
                if ($hd['type'] == 1) { // 免费活动
                    $add['pay_state'] = 1;
                }

                $add['add_time'] = time();
                $bid = $db->add($add);
                if ($bid) {
                    if ($hd['type'] != 1) {
                        $url = 'http://www.xatei666.com/wx_pay/example/huodong.php?bid=' . $bid . '&order_sn=' . $add['paysn'] . '&order_amount=' . $add['money']; // $add['money']
                        redirect($url);
                    } else {
                        $this->redirect('Bm/orderdet', ['bid' => $bid]);
                    }
                } else {
                    die("<script>alert('订单生产失败，请重试！');history.go(-1);</script>");
                }

            } else {
                die("<script>alert('活动信息不存在！');window.location.href='" . U('Index/index') . "';</script>");
            }
        }
    }

    // 订单详情
    public function orderdet(){
        if (IS_GET) {
            $bid = I('get.bid');
            $res = M("baoming")->where(['id' => $bid])->find();
            
            $hd = M('huodong')->where(['id' => $res['hid']])->find();
            $user = M("user")->where(['id' => $_SESSION['user']['id']])->find();
            $this->res = $res;
            $this->hd = $hd;
            $this->user = $user;
            $this->display();
        }
    }

    // 服务条款
    public function tiaokuan(){
        $this->temp = M("women")->where("id=2")->find();
        $this->display();
    }

}