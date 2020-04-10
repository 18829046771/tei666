<?php
namespace Wan\Controller;
use Think\Controller;

class MyController extends PublicController {

    public function index(){
        $userInfo = M("user")->where(['id' => $_SESSION['user']['id']])->find();
        $this->userInfo = $userInfo;
        $this->display();
    }

    // 个人信息
    public function info(){
        $res = M("user")->where(['id' => $_SESSION['user']['id']])->find();
        $this->res = $res;
    	$this->display();
    }

    // 修改信息
    public function edit(){
        if (IS_POST) {
            $data = I('post.');

            $rules = [
                ['sename', 'require', '请输入昵称！'],
                ['photo', 'require', '请输入手机号码！']
            ];

            $db = M("user");

            if (!$db->validate($rules)->create()){
                $this->error($db->getError());
            }else{
                // 上传头像
                if (!empty($_FILES['file']) && isset($_FILES['file'])){
                    $upload = new \Think\Upload([
                        'rootPath'  => './Uploads/tou/',
                        'savePath'  => '',
                        'maxSize'   => 19145728000,
                        'exts'      => array('jpg', 'gif', 'png', 'jpeg')
                    ]);
                    $head = $upload->uploadOne($_FILES['file']);
                    if ($head) {
                        $data['img'] = '/Uploads/tou/'. $head['savepath'] . $head['savename'];
                    }
                }
                $url = U('My/index');
                $res = $db->where(['id' => $_SESSION['user']['id']])->save($data);
                if ($res) {
                    $userInfo = $db->where(['id' => $_SESSION['user']['id']])->find();
                    $_SESSION['user']['sename'] = $userInfo['sename'];
                    $_SESSION['user']['img'] = $userInfo['img'];

                    echo "<script>alert('修改成功！');window.location.href='{$url}'; </script>";die();
                } else {
                    echo "<script>alert('没有修改！');window.location.href='{$url}'; </script>";die();
                }
            }
        }
    }

    // 我报名的活动
    public function play(){
        $list = M('baoming')->where(['uid' => $_SESSION['user']['id']])->order('add_time desc')->select();

        $dataList = [];
        foreach ($list as $key => $value) {
            if ((time() - $value['add_time']) >= 3600 && $value['pay_state'] == 0) { // 收费的活动，用户提交订单之后一小时未支付的，订单自动取消
                continue;
            }
            $dataList[$key] = $value;
            $hd = M('huodong')->where(['id' => $value['hid']])->find(); // 活动信息
            $dataList[$key]['hd'] = $hd;
            $dataList[$key]['tk'] = ($hd['start_time'] - time()) > 259200 ? 1 : 0; // 时间满72小时不支持退款
        }

        $this->dataList = $dataList;
    	$this->display();
    }

    // 取消活动
    public function quxiao(){
        $id = I('id/d');
        if (empty($id)) {
            $this->ajaxReturn(['state' => 0, 'msg' => '参数错误！']);
        }

        $db = M('baoming');

        $map['id'] = $id;
        $map['uid'] = $_SESSION['user']['id'];
        if ($db->where($map)->find()) {
            if ($db->where($map)->delete()) {
                $this->ajaxReturn(['state' => 1, 'msg' => '活动取消成功！']);
            } else {
                $this->ajaxReturn(['state' => 0, 'msg' => '取消失败，请重试！']);
            }
            
        } else {
            $this->ajaxReturn(['state' => 0, 'msg' => '活动信息不存在！']);
        }
    }

    // 退款
    public function tuikuan(){
        $id = I('id/d');
        if (empty($id)) {
            $this->ajaxReturn(['state' => 0, 'msg' => '参数错误！']);
        }

        $db = M('baoming');

        $map['id'] = $id;
        $map['uid'] = $_SESSION['user']['id'];
        if ($db->where($map)->find()) {

            $sdata = [
                'tuikuan' => 1,
                'tuikuan_stime' => time()
            ];
            if ($db->where($map)->save($sdata)) {
                $this->ajaxReturn(['state' => 1, 'msg' => '退款申请成功！']);
            } else {
                $this->ajaxReturn(['state' => 0, 'msg' => '退款失败，请重试！']);
            }
            
        } else {
            $this->ajaxReturn(['state' => 0, 'msg' => '活动信息不存在！']);
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

    // 常见问题
    public function longtime(){
        $temp=M("wenti")->where()->order('time desc')->select();
        $this->temp=$temp;
        $this->display();
    }

    // 关于我们
    public function username(){
        $temp=M("women")->where("id=3")->find();
        $this->temp=$temp;
        $this->display();
    }

}