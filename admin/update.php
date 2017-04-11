<?php
// 注意：使用组件上传，不可以使用 $_FILES["Filedata"]["type"] 来判断文件类型
//mb_http_input("utf-8");
//mb_http_output("utf-8");

//文字水印 
//imageWaterMark($uploadfile,0,"","www.otwcn.com",5,"#FF0000"); 

//图片水印 
$waterImage="../images/watermark.png";//水印图片路径 
//---------------------------------------------------------------------------------------------

$type=strtolower(filekzm($_FILES["Filedata"]["name"]));
if ((($type == ".gif")
|| ($type == ".png")
|| ($type == ".jpeg")
|| ($type == ".jpg")
|| ($type == ".bmp"))
&& ($_FILES["Filedata"]["size"] < 1000000))
  {
  if ($_FILES["Filedata"]["error"] > 0)
    {
    echo "返回错误: " . $_FILES["Filedata"]["error"] . "<br />";
    }
  else
    {
    //echo "上传的文件: " . $_FILES["Filedata"]["name"] . "<br />";
   // echo "文件类型: " . $type . "<br />";
    //echo "文件大小: " . ($_FILES["Filedata"]["size"] / 1024) . " Kb<br />";
   // echo "临时文件: " . $_FILES["Filedata"]["tmp_name"] . "<br />";

    if (file_exists( $_FILES["Filedata"]["name"]))
      {
      echo $_FILES["Filedata"]["name"] . " already exists. ";
      }
    else
      {
	  	$goods_ida=@$_POST["goods_ida"];
		$attachdir = "../images/".date('Ym')."/";
		$attachdir_G = "../images/".date('Ym')."/goods_img/";
		$attachdir_S = "../images/".date('Ym')."/source_img/";
		$attachdir_T = "../images/".date('Ym')."/thumb_img/";
		$ifilename=$goods_ida."_P_".date('YmdHis')."_".rand(10,99).$type;
		if (!file_exists($attachdir)) mkdir($attachdir);
		if (!file_exists($attachdir_G)) mkdir($attachdir_G);
		if (!file_exists($attachdir_S)) mkdir($attachdir_S);
		if (!file_exists($attachdir_T)) mkdir($attachdir_T);
	  	//保存图片
      	//move_uploaded_file($_FILES["Filedata"]["tmp_name"],'./'.$_FILES["Filedata"]["name"]);
		copy($_FILES["Filedata"]["tmp_name"],$attachdir_G.$ifilename);
		copy($_FILES["Filedata"]["tmp_name"],$attachdir_S.$ifilename);
		move_uploaded_file($_FILES["Filedata"]["tmp_name"],$attachdir_T.$ifilename);
		
		//缩略图
		$ic=new ImageCrop($attachdir_T.$ifilename,$attachdir_T.$ifilename);
		$ic->Crop(150,150,3); 
		$ic->SaveImage();  
     	//$ic->SaveAlpha();将补白变成透明像素保
	  	$ic->destory();  
	  	//水印
      	imageWaterMark($attachdir_G.$ifilename,5,$waterImage); 
     	// echo "Stored in: " . $_FILES["Filedata"]["name"]."<br />";
	 	// echo "MD5效验:".getGet("access2008_File_md5")."<br />";
	  	//echo "说明<input type=\"text\"  value=\"".$_FILES["Filedata"]["name"]."\" name=\"img_descA[]\" size=\"5\" />地址<input type=\"text\" name=\"img_urlA[]\"  value=\"".preg_replace("/..\//","",$attachdir_G.$ifilename,1)."\" size=\"20\" /><br>";
	  	echo "地址<input type=\"text\" name=\"img_urlA[]\"  value=\"".preg_replace("/..\//","",$attachdir_G.$ifilename,1)."\" size=\"20\" /><br/>";
      }
    }
  }
else
  {
  echo "上传失败，请检查文件类型和文件大小是否符合标准<br />文件类型：".$type.'<br />文件大小:'.($_FILES["Filedata"]["size"] / 1024) . " Kb<br/>";
  }
  
function filekzm($a)
{
	$c=strrchr($a,'.');
	if($c)
	{
		return $c;
	}else{
		return '';
	}
}
function getPost($v)// 获取POST
{
  if(isset($_POST[$v]))
  {
	  return $_POST[$v];
  }else{
	  return '';
  }
}
//缩略图部分------------------------------------------------------------
/**  
 * Author : smallchicken  
 * Time   : 2009年6月8日16:46:05  
 * mode 1 : 强制裁剪，生成图片严格按照需要，不足放大，超过裁剪，图片始终铺满  
 * mode 2 : 和1类似，但不足的时候 不放大 会产生补白，可以用png消除。  
 * mode 3 : 只缩放，不裁剪，保留全部图片信息，会产生补白，  
 * mode 4 : 只缩放，不裁剪，保留全部图片信息，生成图片大小为最终缩放后的图片有效信息的实际大小，不产生补白  
 * 默认补白为白色，如果要使补白成透明像素，请使用SaveAlpha()方法代替SaveImage()方法  
 * 调用方法：  
 * $ic=new ImageCrop('old.jpg','afterCrop.jpg');  
 * $ic->Crop(120,80,2);  
 * $ic->SaveImage();  
 *      //$ic->SaveAlpha();将补白变成透明像素保存  
 * $ic->destory();  
 *   
 *  
 */  
class ImageCrop{   
       
    var $sImage;   
    var $dImage;   
    var $src_file;   
    var $dst_file;   
    var $src_width;   
    var $src_height;   
    var $src_ext;   
    var $src_type;   
    function ImageCrop($src_file,$dst_file=''){   
        $this->src_file=$src_file;   
        $this->dst_file=$dst_file;   
        $this->LoadImage();   
    }   
       
    function SetSrcFile($src_file){   
        $this->src_file=$src_file;   
    }   
    function SetDstFile($dst_file){   
        $this->dst_file=$dst_file;   
    }   
    function LoadImage(){   
            list($this->src_width, $this->src_height, $this->src_type) = getimagesize($this->src_file);   
            switch($this->src_type) {   
                case IMAGETYPE_JPEG :   
                    $this->sImage=imagecreatefromjpeg($this->src_file);   
                    $this->ext='jpg';   
                    break;   
                case IMAGETYPE_PNG :   
                    $this->sImage=imagecreatefrompng($this->src_file);   
                    $this->ext='png';   
                    break;   
                case IMAGETYPE_GIF :   
                    $this->sImage=imagecreatefromgif($this->src_file);   
                    $this->ext='gif';   
                    break;   
                default:   
                    exit();   
            }   
    }   
    function SaveImage(){   
        switch($this->src_type) {   
            case IMAGETYPE_JPEG :   
                imagejpeg($this->dImage,$this->dst_file,100);   
                break;   
            case IMAGETYPE_PNG :   
                imagepng($this->dImage,$this->dst_file);   
                break;   
            case IMAGETYPE_GIF :   
                imagegif($this->dImage,$this->dst_file);   
                break;   
            default:   
                break;   
        }   
    }   
       
    function SaveAlpha($fileName=''){   
        $this->dst_file=$fileName ? $fileName . '.png' : $this->dst_file .'.png';   
        imagesavealpha($this->dImage, true);   
        imagepng($this->dImage,$this->dst_file);   
    }   
       
    function destory(){   
        imagedestroy($this->sImage);   
        imagedestroy($this->dImage);   
    }   
    function Crop($dst_width,$dst_height,$mode=1,$dst_file=''){   
        if($dst_file) $this->dst_file=$dst_file;   
        $this->dImage = imagecreatetruecolor($dst_width,$dst_height);   
           
        $bg = imagecolorallocatealpha($this->dImage,255,255,255,127);   
        imagefill($this->dImage, 0, 0, $bg);   
        imagecolortransparent($this->dImage,$bg);   
        $ratio_w=1.0 * $dst_width / $this->src_width;   
        $ratio_h=1.0 * $dst_height / $this->src_height;   
        $ratio=1.0;   
        switch($mode){   
            case 1:     // always crop   
                if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)){   
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;   
                    $tmp_w = (int)($dst_width / $ratio);   
                    $tmp_h = (int)($dst_height / $ratio);   
                    $tmp_img=imagecreatetruecolor($tmp_w , $tmp_h);   
                    $src_x = (int) (($this->src_width-$tmp_w)/2) ;    
                    $src_y = (int) (($this->src_height-$tmp_h)/2) ;     
                    imagecopy($tmp_img, $this->sImage, 0,0,$src_x,$src_y,$tmp_w,$tmp_h);    
                    imagecopyresampled($this->dImage,$tmp_img,0,0,0,0,$dst_width,$dst_height,$tmp_w,$tmp_h);   
                    imagedestroy($tmp_img);   
                }else{   
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;   
                    $tmp_w = (int)($this->src_width * $ratio);   
                    $tmp_h = (int)($this->src_height * $ratio);   
                    $tmp_img=imagecreatetruecolor($tmp_w ,$tmp_h);   
                    imagecopyresampled($tmp_img,$this->sImage,0,0,0,0,$tmp_w,$tmp_h,$this->src_width,$this->src_height);   
                    $src_x = (int)($tmp_w - $dst_width) / 2 ;    
                    $src_y = (int)($tmp_h - $dst_height) / 2 ;     
                    imagecopy($this->dImage, $tmp_img, 0,0,$src_x,$src_y,$dst_width,$dst_height);   
                    imagedestroy($tmp_img);   
                }   
                break;   
            case 2:     // only small   
                if($ratio_w < 1 && $ratio_h < 1){   
                    $ratio = $ratio_w < $ratio_h ? $ratio_h : $ratio_w;   
                    $tmp_w = (int)($dst_width / $ratio);   
                    $tmp_h = (int)($dst_height / $ratio);   
                    $tmp_img=imagecreatetruecolor($tmp_w , $tmp_h);   
                    $src_x = (int) ($this->src_width-$tmp_w)/2 ;    
                    $src_y = (int) ($this->src_height-$tmp_h)/2 ;       
                    imagecopy($tmp_img, $this->sImage, 0,0,$src_x,$src_y,$tmp_w,$tmp_h);    
                    imagecopyresampled($this->dImage,$tmp_img,0,0,0,0,$dst_width,$dst_height,$tmp_w,$tmp_h);   
                    imagedestroy($tmp_img);   
                }elseif($ratio_w > 1 && $ratio_h > 1){   
                    $dst_x = (int) abs($dst_width - $this->src_width) / 2 ;    
                    $dst_y = (int) abs($dst_height -$this->src_height) / 2;     
                    imagecopy($this->dImage, $this->sImage,$dst_x,$dst_y,0,0,$this->src_width,$this->src_height);   
                }else{   
                        $src_x=0;$dst_x=0;$src_y=0;$dst_y=0;   
                        if(($dst_width - $this->src_width) < 0){   
                            $src_x = (int) ($this->src_width - $dst_width)/2;   
                            $dst_x =0;   
                        }else{   
                            $src_x =0;   
                            $dst_x = (int) ($dst_width - $this->src_width)/2;   
                        }   
                        if( ($dst_height -$this->src_height) < 0){   
                            $src_y = (int) ($this->src_height - $dst_height)/2;   
                            $dst_y = 0;   
                        }else{   
                            $src_y = 0;   
                            $dst_y = (int) ($dst_height - $this->src_height)/2;   
                        }   
                    imagecopy($this->dImage, $this->sImage,$dst_x,$dst_y,$src_x,$src_y,$this->src_width,$this->src_height);   
                }   
                break;   
            case 3:     // keep all image size and create need size   
                if($ratio_w > 1 && $ratio_h > 1){   
                    $dst_x = (int)(abs($dst_width - $this->src_width )/2) ;    
                    $dst_y = (int)(abs($dst_height- $this->src_height)/2) ;   
                    imagecopy($this->dImage, $this->sImage, $dst_x,$dst_y,0,0,$this->src_width,$this->src_height);   
                }else{   
                    $ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;   
                    $tmp_w = (int)($this->src_width * $ratio);   
                    $tmp_h = (int)($this->src_height * $ratio);   
                    $tmp_img=imagecreatetruecolor($tmp_w ,$tmp_h);   
                    imagecopyresampled($tmp_img,$this->sImage,0,0,0,0,$tmp_w,$tmp_h,$this->src_width,$this->src_height);   
                    $dst_x = (int)(abs($tmp_w -$dst_width )/2) ;    
                    $dst_y = (int)(abs($tmp_h -$dst_height)/2) ;   
                    imagecopy($this->dImage, $tmp_img, $dst_x,$dst_y,0,0,$tmp_w,$tmp_h);   
                    imagedestroy($tmp_img);   
                }   
                break;   
            case 4:     // keep all image but create actually size   
                if($ratio_w > 1 && $ratio_h > 1){   
                    $this->dImage = imagecreatetruecolor($this->src_width,$this->src_height);   
                    imagecopy($this->dImage, $this->sImage,0,0,0,0,$this->src_width,$this->src_height);   
                }else{   
                    $ratio = $ratio_w > $ratio_h ? $ratio_h : $ratio_w;   
                    $tmp_w = (int)($this->src_width * $ratio);   
                    $tmp_h = (int)($this->src_height * $ratio);   
                    $this->dImage = imagecreatetruecolor($tmp_w ,$tmp_h);   
                    imagecopyresampled($this->dImage,$this->sImage,0,0,0,0,$tmp_w,$tmp_h,$this->src_width,$this->src_height);   
                }   
                break;   
        }   
    }// end Crop   
  
}   
//缩略图结束----------------------------------------------------- 
/**
* 功能：PHP图片水印 (水印支持图片或文字) 
* 参数： 
*       $groundImage     背景图片，即需要加水印的图片，暂只支持GIF,JPG,PNG格式； 
*       $waterPos         水印位置，有10种状态，0为随机位置； 
*                         1为顶端居左，2为顶端居中，3为顶端居右； 
*                         4为中部居左，5为中部居中，6为中部居右； 
*                         7为底端居左，8为底端居中，9为底端居右； 
*       $waterImage         图片水印，即作为水印的图片，暂只支持GIF,JPG,PNG格式； 
*       $waterText         文字水印，即把文字作为为水印，支持ASCII码，不支持中文； 
*       $textFont         文字大小，值为1、2、3、4或5，默认为5； 
*       $textColor         文字颜色，值为十六进制颜色值，默认为#FF0000(红色)； 
* 
* 注意：Support GD 2.0，Support FreeType、GIF Read、GIF Create、JPG 、PNG 
*       $waterImage 和 $waterText 最好不要同时使用，选其中之一即可，优先使用 $waterImage。 
*       当$waterImage有效时，参数$waterString、$stringFont、$stringColor均不生效。 
*       加水印后的图片的文件名和 $groundImage 一样。 
* 
*/ 
function imageWaterMark($groundImage,$waterPos=0,$waterImage="",$waterText="",$textFont=5,$textColor="#FF0000") 
{ 
    $isWaterImage = FALSE; 
    $formatMsg = "暂不支持该文件格式，请用图片处理软件将图片转换为GIF、JPG、PNG格式。"; 

    //读取水印文件 
    if(!empty($waterImage) && file_exists($waterImage)) 
     { 
        $isWaterImage = TRUE; 
        $water_info = getimagesize($waterImage); 
        $water_w    = $water_info[0];//取得水印图片的宽 
        $water_h    = $water_info[1];//取得水印图片的高 

        switch($water_info[2])//取得水印图片的格式 
        { 
             case 1:$water_im = imagecreatefromgif($waterImage);break; 
             case 2:$water_im = imagecreatefromjpeg($waterImage);break; 
             case 3:$water_im = imagecreatefrompng($waterImage);break; 
             default:die($formatMsg); 
         } 
     } 

    //读取背景图片 
    if(!empty($groundImage) && file_exists($groundImage)) 
     { 
        $ground_info = getimagesize($groundImage); 
        $ground_w    = $ground_info[0];//取得背景图片的宽 
        $ground_h    = $ground_info[1];//取得背景图片的高 

        switch($ground_info[2])//取得背景图片的格式 
        { 
             case 1:$ground_im = imagecreatefromgif($groundImage);break; 
             case 2:$ground_im = imagecreatefromjpeg($groundImage);break; 
             case 3:$ground_im = imagecreatefrompng($groundImage);break; 
             default:die($formatMsg); 
         } 
     } 
     else 
     { 
         die("需要加水印的图片不存在！"); 
     } 

    //水印位置 
    if($isWaterImage)//图片水印 
    { 
        $w = $water_w; 
        $h = $water_h; 
        $label = "图片的"; 
     } 
     else//文字水印 
    { 
        $temp = @imagettfbbox(ceil($textFont*2.5),0,"./cour.ttf",$waterText);//取得使用 TrueType 字体的文本的范围 
        $w = $temp[2] - $temp[6]; 
        $h = $temp[3] - $temp[7]; 
         unset($temp); 
        $label = "文字区域"; 
     } 
     if( ($ground_w<$w) || ($ground_h<$h) ) 
     { 
         echo "需要加水印的图片的长度或宽度比水印".$label."还小，无法生成水印！"; 
         return; 
     } 
     switch($waterPos) 
     { 
         case 0://随机 
            $posX = rand(0,($ground_w - $w)); 
            $posY = rand(0,($ground_h - $h)); 
             break; 
         case 1://1为顶端居左 
            $posX = 0; 
            $posY = 0; 
             break; 
         case 2://2为顶端居中 
            $posX = ($ground_w - $w) / 2; 
            $posY = 0; 
             break; 
         case 3://3为顶端居右 
            $posX = $ground_w - $w; 
            $posY = 0; 
             break; 
         case 4://4为中部居左 
            $posX = 0; 
            $posY = ($ground_h - $h) / 2; 
             break; 
         case 5://5为中部居中 
            $posX = ($ground_w - $w) / 2; 
            $posY = ($ground_h - $h) / 2; 
             break; 
         case 6://6为中部居右 
            $posX = $ground_w - $w; 
            $posY = ($ground_h - $h) / 2; 
             break; 
         case 7://7为底端居左 
            $posX = 0; 
            $posY = $ground_h - $h; 
             break; 
         case 8://8为底端居中 
            $posX = ($ground_w - $w) / 2; 
            $posY = $ground_h - $h; 
             break; 
         case 9://9为底端居右 
            $posX = $ground_w - $w; 
            $posY = $ground_h - $h; 
             break; 
         default://随机 
            $posX = rand(0,($ground_w - $w)); 
            $posY = rand(0,($ground_h - $h)); 
             break;     
     } 

    //设定图像的混色模式 
    imagealphablending($ground_im, true); 

     if($isWaterImage)//图片水印 
    { 
        imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $water_w,$water_h);//拷贝水印到目标文件         
    } 
     else//文字水印 
    { 
         if( !empty($textColor) && (strlen($textColor)==7) ) 
         { 
            $R = hexdec(substr($textColor,1,2)); 
            $G = hexdec(substr($textColor,3,2)); 
            $B = hexdec(substr($textColor,5)); 
         } 
         else 
         { 
             die("水印文字颜色格式不正确！"); 
         } 
        imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate($ground_im, $R, $G, $B));         
     } 

    //生成水印后的图片 
    @unlink($groundImage); 
     switch($ground_info[2])//取得背景图片的格式 
    { 
         case 1:imagegif($ground_im,$groundImage);break; 
         case 2:imagejpeg($ground_im,$groundImage);break; 
         case 3:imagepng($ground_im,$groundImage);break; 
         default:die("取得背景图片的格式不在处理范围内"); 
     } 

    //释放内存 
    if(isset($water_info)) unset($water_info); 
     if(isset($water_im)) imagedestroy($water_im); 
     unset($ground_info); 
    imagedestroy($ground_im); 
} 
//以上是php 加水印的函数

?>

