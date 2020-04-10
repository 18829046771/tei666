<?php
namespace Admin\Controller;
use Think\Controller;
class CommController extends Controller {

    public function _initialize(){

        if(!isset($_SESSION['system_user_nickname'])){
            $this->error('您还未登陆，请先登录！',U('Login/index'),1);
            exit;
        }


        $menu_list=$this->menu_list();

        $this->assign('menu_list',$menu_list);
    }


    function menu_list(){

        $arr=array();
         $arr[]=array(
            'text'=>'用户管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'会员用户列表',
                    'url'=>'Yonghu/index',
                    'actived'=>0
                ),
                array('text'=>'普通用户列表',
                    'url'=>'Yonghu/putong',
                    'actived'=>0
                )
                 
                
            )
        );
        
        $arr[]=array(
            'text'=>'营销管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'限时购',
                    'url'=>'yingxiao/xianshi',
                    'actived'=>0
                ),
                 array('text'=>'整点秒杀',
                    'url'=>'yingxiao/miaosha',
                    'actived'=>0
                )
                 
                
            )
        );
        
         $arr[]=array(
            'text'=>'图片管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'图片列表',
                    'url'=>'PictureManagement/tupian',
                    'actived'=>0
                )
                 
                
            )
        );
        
        $arr[]=array(
            'text'=>'门店管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'门店列表',
                    'url'=>'Mendian/index',
                    'actived'=>0
                ),
                array('text'=>'门店订单',
                    'url'=>'Mendian/dingdan',
                    'actived'=>0
                )
                 
                
            )
        );
        $arr[]=array(
            'text'=>'订单列表',
            'actived'=>0,
            'child'=>array(
                array('text'=>'旅游订单',
                    'url'=>'BrandManagement/index',
                    'actived'=>0
                ),
//              array('text'=>'保险订单',
//                  'url'=>'BrandManagement/baoxian_index',
//                  'actived'=>0
//              ),
//               array('text'=>'活动订单',
//                  'url'=>'BrandManagement/huodong_index',
//                  'actived'=>0
//              ),
                array('text'=>'汽车订单',
                    'url'=>'BrandManagement/qiche_index',
                    'actived'=>0
                )
                
            )
        );
        
        $arr[]=array(
            'text'=>'物流管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'物流发货',
                    'url'=>'CasesManagement/index',
                    'actived'=>0
                ),
                array('text'=>'订单查询',
                    'url'=>'CasesManagement/cha',
                    'actived'=>0
                ),
                array('text'=>'收货订单',
                    'url'=>'CasesManagement/shouhuo',
                    'actived'=>0
                ),
                

            )
        );
        
        $arr[]=array(
            'text'=>'退款管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'退款列表',
                    'url'=>'PictureManagement/index',
                    'actived'=>0
                )
            )
        );
        $arr[]=array(
          'text'=>'供应商管理',
          'actived'=>0,
          'child'=>array(
             
              array('text'=>'旅游供应商',
                  'url'=>'Log/gongyin',
                  'actived'=>0
              ),
               array('text'=>'车管家供应商',
                  'url'=>'Log/gongyin_che',
                  'actived'=>0
              ),
               array('text'=>'其他供应商',
                  'url'=>'Log/gongyin_qi',
                  'actived'=>0
              ),
//             array('text'=>'城会玩供应商',
//                'url'=>'Log/gongyin_cheng',
//                'actived'=>0
//            ),
//              array('text'=>'保险供应商',
//                'url'=>'Log/gongyin_bao',
//                'actived'=>0
//            )

          )
        ); 
        $arr[]=array(
          'text' => '服务商管理',
          'actived' => 0,
          'child' => array(
              array('text'=>'旅游服务商',
                  'url'=>'Fuwu/fuwu_ly',
                  'actived'=>0
              ),
               array('text'=>'车管家服务商',
                  'url'=>'Fuwu/fuwu_che',
                  'actived'=>0
              ),
               array('text'=>'其他服务商',
                  'url'=>'Fuwu/fuwu_qi',
                  'actived'=>0
              ),
//             array('text'=>'城会玩服务商',
//                'url'=>'Fuwu/fuwu_cheng',
//                'actived'=>0
//            ),
//              array('text' => '保险服务商',
//                'url' => 'Fuwu/fuwu_bao',
//                'actived' => 0
//            )

          )
        ); 
          $arr[]=array(
            'text'=>'公司产品',
            'actived'=>0,
            'child'=>array(
                array('text'=>'旅游产品管理',
                    'url'=>'productManagement/index',
                    'actived'=>0
                ),
                array('text'=>'车产品管理',
                    'url'=>'productManagement/index_che',
                    'actived'=>0
                ),
                array('text'=>'忒娃产品管理',
                    'url'=>'productManagement/index_teiwa',
                    'actived'=>0
                ),
//              array('text'=>'保险管理',
//                  'url'=>'productManagement/index_baoxian',
//                  'actived'=>0
//              ),
                array('text'=>'其他产品管理',
                    'url'=>'Links/qita_index',
                    'actived'=>0
                ),
                 array('text'=>'热卖/推荐商品',
                    'url'=>'Tui/index',
                    'actived'=>0
                )
                

            )
        );

        $arr[]=array(
            'text'=>'积分商城',
            'actived'=>0,
            'child'=>array(
                array('text'=>'积分商品',
                    'url'=>'point/goodsList',
                    'actived'=>0
                ),
                array('text'=>'订单列表',
                    'url'=>'point/orderList',
                    'actived'=>0
                ),
                array('text'=>'物流发货',
                    'url'=>'point/orderList_fh',
                    'actived'=>0
                )
            )
        );
          
        $arr[]=array(
          'text'=>'私人订制',
          'actived'=>0,
          'child'=>array(
             
              array('text'=>'订制列表',
                  'url'=>'Links/siren',
                  'actived'=>0
              )

          )
        );
          
          
          
             
//        $arr[]=array(
//            'text'=>'产品属性管理',
//            'actived'=>0,
//            'child'=>array(
//               
//
//                array('text'=>'产品属性列表',
//                    'url'=>'Links/liebiao',
//                    'actived'=>0
//                )
//
//            )
//        );
           $arr[]=array(
              'text'=>'节目管理',
              'actived'=>0,
              'child'=>array(
                 
                  array('text'=>'节目列表',
                      'url'=>'Links/jiemu_index',
                      'actived'=>0
                  )

              )
          );
        $arr[]=array(
            'text'=>'评论管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'评论列表',
                    'url'=>'Message/index',
                    'actived'=>0
                )

            )
        );
         $arr[]=array(
            'text'=>'关于我们',
            'actived'=>0,
            'child'=>array(
                array('text'=>'关于我们',
                    'url'=>'Message/guanyu',
                    'actived'=>0
                ),
                array('text'=>'常见问题',
                    'url'=>'changjian/index',
                    'actived'=>0
                )
               

            )
        );

//      $arr[]=array(
//          'text'=>'城会玩',
//          'actived'=>0,
//          'child'=>array(
//              array('text'=>'活动列表',
//                  'url'=>'NewsManagement/index',
//                  'actived'=>0
//              )
//
//          )
//      );
        

       
        
        $arr[]=array(
            'text'=>'财务管理',
            'actived'=>0,
            'child'=>array(
//              array('text'=>'保险订单',
//                  'url'=>'Log/biaoxian_index',
//                  'actived'=>0
//              ),
//              array('text'=>'活动订单',
//                  'url'=>'Log/huodong_index',
//                  'actived'=>0
//              ),
                array('text'=>'旅游订单',
                    'url'=>'Log/lvyou_index',
                    'actived'=>0
                ),
                array('text'=>'汽车订单',
                    'url'=>'Log/che_index',
                    'actived'=>0
                ),
                array('text'=>'退款订单',
                    'url'=>'Log/tui_index',
                    'actived'=>0
                ),
            )
        );

        $arr[]=array(
            'text'=>'分类管理',
            'actived'=>0,
            'child'=>array(
                array('text'=>'分类管理',
                    'url'=>'Cate/cateList',
                    'actived'=>0
                )
            )
        );


    

//      $arr[]=array(
//          'text'=>'活动',
//          'actived'=>0,
//          'child'=>array(
//              array('text'=>'基础设置',
//                  'url'=>'Jcsz/index',
//                  'actived'=>0
//              )
//          )
//      );

        $arr[]=array(
            'text'=>'系统设置',
            'actived'=>0,
            'child'=>array(
                array('text'=>'用户管理',
                    'url'=>'User/index',
                    'actived'=>0
                ),
                array('text'=>'登录日志',
                    'url'=>'Log/index',
                    'actived'=>0
                )
            )
        );

        //获取当前的文件路径

        $currentUrl=str_replace('/index.php/Admin/','',__ACTION__);
        //判断获取的文件路径是否等于现在的路径
        foreach($arr as $key=>$value){
            foreach($value['child'] as $k=>$v){
                if($v['url']==$currentUrl){
                    $arr[$key]['actived']=1;
                    $arr[$key]['child'][$k]['actived']=1;
                    break;
                }
            }
        }

        return $arr;

   }
}