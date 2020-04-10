<?php
namespace Admin\Controller;
use Think\Controller;
class TreeController extends CommController {


//这里是递归方法
    public $tree = null;
    function createtree(array $data = null, $lv = 1) {

        for ($i = 0; $i < count($data); $i++) {
            //dump($lv);

            for($j=1;$j<=$lv;$j++){

                if($j==1){
                    $str='';
                }else{
                    $str='----';
                }
                $data[$i]['cate_name']=$str.$data[$i]['cate_name'];
            }
            $data[$i]['lv'] = $lv;

            $this->tree[count($this->tree)] =$data[$i];
            $res = M('cate')->where('cate_p_id='.$data[$i]['cate_id'])->select();
            $this->createtree($res, ($lv + 1));
        }
    }
    function createtree2(array $data = null, $lv = 1) {

        for ($i = 0; $i < count($data); $i++) {
            //dump($lv);

            for($j=1;$j<=$lv;$j++){

                if($j==1){
                    $str='';
                }else{
                    $str='----';
                }
                $data[$i]['l_cate_name']=$str.$data[$i]['l_cate_name'];
            }
            $data[$i]['lv'] = $lv;

            $this->tree[count($this->tree)] =$data[$i];
            $res = M('link_c')->where('l_cate_p_id='.$data[$i]['l_cate_id'])->select();
            $this->createtree2($res, ($lv + 1));
        }
    }




}