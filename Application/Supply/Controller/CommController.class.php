<?php
namespace Supply\Controller;
use Think\Controller;

class CommController extends Controller{

	public $supply_info;

	public function _initialize(){
		$supply_id = intval($_SESSION['supply_id']);
		if(!isset($supply_id) || empty($supply_id)){
			$this->error('您还未登陆，请先登录！', U('Login/index'), 1);die();
		}
		// 供应商信息
		$supply_info = M('gongying')->where(['id' => $supply_id])->field('id,cate_id,name,photo,user,username,time')->find();
		$this->supply_info = $supply_info;
		$this->assign('supply_info', $supply_info);

		// 菜单
		$menu_list = $this->menu_list();
		$this->assign('menu_list', $menu_list);
	}

	public function menu_list(){
		$arr = [];
		$arr[] = [
			'text' => '产品管理',
			'actived' => 0,
			'icon' => 'fa fa-align-justify',
			'child' => [
				[
					'text' => '产品列表',
					'url' => 'Product/lis',
					'actived' => 0
				]
			]
		];
		$arr[] = [
			'text' => '安全管理',
			'actived' => 0,
			'icon' => 'fa fa-lock',
			'child' => [
				[
					'text' => '修改密码',
					'url' => 'User/chenge',
					'actived' => 0
				]
			]
		];
		// 获取当前的文件路径
		$currentUrl = str_replace('/index.php/Supply/', '', __ACTION__);
		// 判断获取的文件路径是否等于现在的路径
		foreach($arr as $key => $value){
			foreach($value['child'] as $k => $v){
				if($v['url'] == $currentUrl){
					$arr[$key]['actived'] = 1;
					$arr[$key]['child'][$k]['actived'] = 1;
					break;
				}
			}
		}
		return $arr;
	}

    public function getExcel($fileName, $headArr, $data) {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入   这三个文件放在thinkphp/library/org/phpExcel
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");
        $date = date("Y_m_d_H_i_s", time());
        $fileName.= "_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        //设置表头  超过26列
        $key = 0;
        foreach ($headArr as $v) {
            //注意，不能少了。将列数字转换为字母\
            $colum = \PHPExcel_Cell::stringFromColumnIndex($key);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key+= 1;
        }
        $column = 2; //从第二行写入数据 第一行是表头
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach ($data as $key => $rows) { //行写入
            $span = 0;
            foreach ($rows as $keyName => $value) { // 列写入
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                $objActSheet->setCellValue($j . $column, $value);
                $span++;
            }
            $column++;
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

}