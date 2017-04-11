<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');


/*------------------------------------------------------ */
//-- seo列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{

    $optimize_list = optimize_list();
    $smarty->assign('ur_here','seo列表');
    $smarty->assign('action_link', array('href' => 'optimize.php?act=add', 'text' => 'seo添加'));
    $smarty->assign('optimize_list',   $optimize_list['optimize']);
    $smarty->assign('full_page',    1);
    $smarty->display('optimize_list.htm');

}
elseif ($_REQUEST['act'] == 'add')
{
    $smarty->assign('step','add');
    $smarty->display('optimize_info.htm');

}
else if ($_REQUEST['act'] == 'edit')//编辑门店页面
{
    $id = $_GET['id'];
    $sql1 = "select * from {$ecs->table('seo')} where id={$id}";
    $row = $db->getRow($sql1);
    $smarty->assign('row',$row);
    $smarty->assign('is_edit',1);
    $smarty->assign('step',edit);

    $smarty->display('optimize_info.htm');
}
else if ($_REQUEST['act'] == 'add_exe')
{
    $type       =   $_POST['type'];
    $title      =   $_POST['title'];
    $keywords   =   $_POST['keywords'];
    $describe   =   $_POST['describe'];

    $data['type']  =$type;
    $data['title'] =$title;
    $data['keywords'] =$keywords;
    $data['descri'] =$describe;

    $res = $db->autoExecute($ecs->table('seo'),$data,'INSERT');

    if($res){
        $link[0]['text'] = '返回到seo列表页';
        $link[0]['href'] = '?act=list';
        sys_msg('添加成功',0,$link);
    }else{
        sys_msg('添加失败',1,$link);
    }

}
else if ($_REQUEST['act'] == 'edit_exe')//编辑执行
{
    $id = $_POST['id'];
    $type       =   $_POST['type'];
    $title      =   $_POST['title'];
    $keywords   =   $_POST['keywords'];
    $describe   =   $_POST['describe'];
    $data=array(
        'type'      =>  $type,
        'title'     =>  $title,
        'keywords'  =>  $keywords,
        'descri'  =>  $describe
    );
    $res = $db->autoExecute($ecs->table('seo'),$data,'UPDATE',"id={$id}");
    if($res){
        sys_msg('编辑成功',0,$link);
    }else{
        sys_msg('编辑失败',1,$link);
    }
}
elseif ($_REQUEST['act'] == 'remove')
{

    $id = intval($_GET['id']);

    $sql = "DELETE FROM " .$ecs->table('seo'). " WHERE id = '$id'";
    $res = $db->query($sql);
    $url = 'optimize.php?act=list';
    ecs_header("Location: $url\n");
    exit;
}
else if ($_REQUEST['act'] == 'query')
{
    $optimize_list = optimize_list();
    $smarty->assign('optimize_list',   $optimize_list['optimize']);
    make_json_result($smarty->fetch('store_list.htm'));
}
function optimize_list()
{
    $sql='select * from ecs_seo';
    $row = $GLOBALS['db']->getAll($sql);
    return array('optimize' => $row);

}


?>