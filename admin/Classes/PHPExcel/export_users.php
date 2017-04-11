<?php

define('IN_ECS', true);

require_once(dirname(__FILE__) . '/includes/init.php');
require_once(dirname(__FILE__) . '/phpexcel/PHPExcel.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$sql = "SELECT user_id,user_rank,user_name,nickname,parent_id,wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_rank=102";
$parent_arr = $db->getAll($sql);

foreach ($parent_arr as &$value) {
	$value['childnum'] = 0;
	get_vip_of_user($value);
	get_children_of_user($value, $value);
}

// echo json_encode($parent_arr);

// Create new PHPExcel object
$phpExcel = new PHPExcel();
$phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '会员编号')
            ->setCellValue('B1', '会员名称')
            ->setCellValue('C1', '会员等级')
            ->setCellValue('D1', '上级编号')
            ->setCellValue('E1', '会员昵称')
            ->setCellValue('F1', '微信openid')
            ->setCellValue('G1', 'VIP数量')
            ->setCellValue('H1', '下级会员数量');

$cell_index = 2;
foreach ($parent_arr as $value) {
	add_cell_data($value);
}

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="会员.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
$objWriter->save('php://output');

function get_children_of_user(&$top, &$parent) {
	global $db;

	$sql = "SELECT user_id,user_rank,user_name,nickname,parent_id,wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE parent_id=" . $parent['user_id'];
	$parent['children'] = $db->getAll($sql);

	if (count($parent['children']) > 0) {
		$top['childnum'] += count($parent['children']);

		foreach ($parent['children'] as &$value) {
			get_children_of_user($top, $value);
		}
	}
}

function get_vip_of_user(&$parent) {
	global $db;

	$sql = "SELECT user_id,user_rank,user_name,nickname,parent_id,wxid FROM " . $GLOBALS['ecs']->table('users') . " WHERE parent_id=" . $parent['user_id'] . " AND user_rank=99";
	$parent['children'] = $db->getAll($sql);
	$parent['vipnum'] = count($parent['children']);
	// $parent['childnum'] = 0;

	// if (count($parent['children']) > 0) {
	// 	$parent['vipnum'] = count($parent['children']);
	// 	$parent['childnum'] += count($parent['children']);

	// 	foreach ($parent['children'] as &$value) {
	// 		get_children_of_user($parent, $value);
	// 	}
	// }
}

function get_user_rank_name($user_rank) {
	if (intval($user_rank) == 99) {
		return 'vip会员';
	} elseif (intval($user_rank) == 102) {
		return '加盟商';
	} elseif (intval($user_rank) == 103) {
		return '服务商';
	} else {
		return '普通会员';
	}
}

function add_cell_data($parent, $prefix = '') {
	global $phpExcel;
	global $cell_index;

	$phpExcel->setActiveSheetIndex(0)
	            ->setCellValue('A'.$cell_index, $prefix . $parent['user_id'])
	            ->setCellValue('B'.$cell_index, $parent['user_name'])
	            ->setCellValue('C'.$cell_index, get_user_rank_name($parent['user_rank']))
	            ->setCellValue('D'.$cell_index, $parent['parent_id'])
	            ->setCellValue('E'.$cell_index, $parent['nickname'])
	            ->setCellValue('F'.$cell_index, $parent['wxid']);

	if (isset($parent['vipnum'])) {
		$phpExcel->setActiveSheetIndex(0)->setCellValue('G'.$cell_index, $parent['vipnum']);
	}

	if (isset($parent['childnum'])) {
		$phpExcel->setActiveSheetIndex(0)->setCellValue('H'.$cell_index, $parent['childnum']);
	}
	            
    $phpExcel->setActiveSheetIndex(0)->getStyle('A'.$cell_index)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)));
	$cell_index++;

	if (count($parent['children']) > 0) {
		foreach ($parent['children'] as $value) {
			add_cell_data($value, $prefix . ' - ');
		}
	}
}

?>