<?php
namespace Admin\Controller;
use Think\Controller;
class Md5Controller extends Controller {

    //MD5 特殊加密方法
    public static function to_md5($str)
    {
        //进行2次MD5加密
        $str = md5(md5($str));
        //产生随机的2个字符
        $str_all   = 'abcd0ef1gh2ij3kl4mno5pqr6st7uv8wx9yz';
        $str_rand  = $str_all[rand(0,35)].$str_all[rand(0,35)];
        //产生随机位置
        $set = rand(10,30);
        //将MD5结果拆分并组合
        $md5_f = substr($str,0,$set);
        $md5_b = substr($str,$set);
        return $md5_f.$str_rand.$md5_b.$set;
    }

    //MD5 还原方法
    public static function get_md5($str)
    {
        //46cc468df66b0c961d8da2326337c7aa5810
        //
        //1 获取随机位置
        $set   = substr($str,-2);
        $str   = substr($str,0,34);
        $md5_f = substr($str,0,$set);
        $md5_b = substr($str,$set+2);
        return $md5_f.$md5_b;

    }

    public static function md5_to2($str)
    {
        return md5(md5($str));
    }

}