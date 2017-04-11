<!DOCTYPE html>
<html>
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta charset="utf-8" />
<title>美美商城</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<link href="themesmobile/mobile/img/touch-icon.png" rel="apple-touch-icon-precomposed" />
<link href="themesmobile/mobile/img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link href="themesmobile/mobile/css/ectouch.css" rel="stylesheet" type="text/css" />

<script src="/js/transport.js"></script>
<?php echo $this->smarty_insert_scripts(array('files'=>'transport.js,common.js,user.js')); ?>
<script src="themesmobile/js/lib/jquery-1.9.1.min.js"></script>
<script src="themesmobile/mobile/js/region.js"></script>  
</head>
<body>
<div id="tbh5v0">
  <div class="login"> 
     
    <?php if ($this->_var['action'] == 'profile'): ?> 
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?> 
    <script type="text/javascript">
          <?php $_from = $this->_var['lang']['profile_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
            var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </script>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['profile']; ?> </h1>
    </header>
    <div class="blank3"></div>
    <section class="wrap">
      <div class="blank3"></div>
      <form name="formPassword" action="user.php" method="post" onSubmit="return editPassword()" >
         <section class="order_box padd1 radius10" id="modify" >
          <div class="table_box table_box2">
            <dl>
              <dd class="dd1">用户名:</dd>
              <input name="user_name" type="text" value="<?php echo $this->_var['profile']['user_name']; ?>" style="border:1px solid #ddd;height:2rem"  class="dd2"  readonly="true"/>
            </dl>
            <dl>
              <dd class="dd1">联系电话:</dd>
              <input name="user_phone" type="text" value="<?php if ($this->_var['profile']['mobile_phone'] == ''): ?> <?php echo $this->_var['profile']['address']['0']['mobile']; ?> <?php else: ?> <?php echo $this->_var['profile']['mobile_phone']; ?> <?php endif; ?>" style="border:1px solid #ddd;height:2rem"    class="dd2"  /><em style="color:red">*</em>
            </dl>
            <dl>
              <dd class="dd1">配送地址:</dd>
              <table align="center" >
                  <tr>
                      <td align="left" >
                        <select name="country" id="selCountries_<?php echo $this->_var['sn']; ?>" onchange="region.changed(this, 1, 'selProvinces_<?php echo $this->_var['sn']; ?>')" style="display:none;" >
                          <option value="0">国家</option>
                          <?php $_from = $this->_var['country_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'country');if (count($_from)):
    foreach ($_from AS $this->_var['country']):
?>
                          <option value="<?php echo $this->_var['country']['region_id']; ?>" <?php if ($this->_var['country']['region_id'] == 1): ?>selected<?php endif; ?>><?php echo $this->_var['country']['region_name']; ?></option>
                          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                         </select>
                          
                         <select name="province" id="selProvinces_<?php echo $this->_var['sn']; ?>" onchange="region.changed(this, 2, 'selCities_<?php echo $this->_var['sn']; ?>')" >
                          <?php if ($this->_var['province']): ?>
                          <option value="<?php echo $this->_var['profile']['address']['province']; ?>" selected><?php echo $this->_var['province']; ?></option>
                          <?php else: ?>
                          <option value="0">省份</option>
                          <?php endif; ?>
                          <?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'province');if (count($_from)):
    foreach ($_from AS $this->_var['province']):
?>
                          <option value="<?php echo $this->_var['province']['region_id']; ?>" ><?php echo $this->_var['province']['region_name']; ?></option>
                          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                         </select>
                          
                         <select name="city" id="selCities_<?php echo $this->_var['sn']; ?>" onchange="region.changed(this, 3, 'selDistricts_<?php echo $this->_var['sn']; ?>')" >
                          <?php if ($this->_var['city']): ?>
                          <option value="<?php echo $this->_var['profile']['address']['city']; ?>"><?php echo $this->_var['city']; ?></option>
                          <?php else: ?>
                          <option value="0">市</option>
                          <?php endif; ?>
                         </select>
                          
                         <select name="district" id="selDistricts_<?php echo $this->_var['sn']; ?>"  onchange="ajax_store(this.value)">
                          <?php if ($this->_var['district']): ?>
                          <option value="<?php echo $this->_var['profile']['address']['district']; ?>"><?php echo $this->_var['district']; ?></option>
                          <?php else: ?>
                          <option value="0">区</option>
                          <?php endif; ?>
                         </select>
                     </td>
                 </tr>
          </table>
            </dl>
            <dl>
              <dd class="dd1">详细地址:</dd>
              <input  name="address" type="text" value="<?php echo $this->_var['address']; ?>" style="border:1px solid #ddd;height:2rem" class="dd2" /><em style="color:red">*</em>
            </dl>
             <dl>
              <dd class="dd1">新密码:</dd>
              <input  name="new_password" type="text" style="border:1px solid #ddd;height:2rem" class="dd2" /><em style="color:red"></em>
            </dl>
            
             <dl>
              <dd class="dd1">开户人:</dd>
              <input  name="card_name" type="text" style="border:1px solid #ddd;height:2rem" class="dd2" value="<?php echo $this->_var['profile']['card_name']; ?>" placeholder="XXX"/><em style="color:red"></em>
            </dl>
             <dl>
              <dd class="dd1">银行卡号:</dd>
              <input  name="card_no" type="text" style="border:1px solid #ddd;height:2rem" class="dd2" value="<?php echo $this->_var['profile']['card_no']; ?>" placeholder="622XXXXXXXXXXXXXXX" maxlength="19"/><em style="color:red"></em>
            </dl>
            <dl>
              <dd class="dd1">开户行:</dd>
              <input  name="bank_name" type="text" style="border:1px solid #ddd;height:2rem" value="<?php echo $this->_var['profile']['bank_name']; ?>" class="dd2" placeholder="XX银行XX区XX支行"/><em style="color:red"></em>
            </dl>
            
          </div>
        </section>
        <div class="blank3"></div>
        <input name="act" type="hidden" value="act_edit_password" />
        <input name="submit" type="submit" class="c-btn3"     value="<?php echo $this->_var['lang']['confirm_edit']; ?>" />
      </form>
    </section>
    <?php endif; ?> 
     
    <?php if ($this->_var['action'] == 'bonus'): ?> 
    <script type="text/javascript">
        <?php $_from = $this->_var['lang']['profile_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
          var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </script>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['label_bonus']; ?> </h1>
    </header>
    <section class="wrap bonus_list">
	  <section class="order_box padd1 radius10 single_item">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
        <tr>
          <th align="center">序号</th>
          <th align="center">名称</th>
          <th align="center">金额</th>
          <th align="center">最小订单</th>
          <th align="center">截止日期</th>
          <th align="center">状态</th>
        </tr>
        <?php if ($this->_var['bonus']): ?> 
        <?php $_from = $this->_var['bonus']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <tr>
          <td align="center"><?php echo empty($this->_var['item']['bonus_sn']) ? 'N/A' : $this->_var['item']['bonus_sn']; ?></td>
          <td align="center"><?php echo $this->_var['item']['type_name']; ?></td>
          <td align="center"><?php echo $this->_var['item']['type_money']; ?></td>
          <td align="center"><?php echo $this->_var['item']['min_goods_amount']; ?></td>
          <td align="center"><?php echo $this->_var['item']['use_enddate']; ?></td>
          <td align="center"><?php echo $this->_var['item']['status']; ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        <?php else: ?>
        <tr>
          <td colspan="6" bgcolor="#FFFFFF"><?php echo $this->_var['lang']['user_bonus_empty']; ?></td>
        </tr>
        <?php endif; ?>
      </table>
      </section>

	  <section class="order_box padd1 radius10">
      <form name="addBouns" action="user.php" method="post" onSubmit="return addBonus()" style="text-align:center">
        <input  placeholder="<?php echo $this->_var['lang']['bonus_number']; ?>" name="bonus_sn" type="text" class="inputBg_touch" style="margin-bottom:10px" />
        <input type="hidden" name="act" value="act_add_bonus" class="inputBg" />
        <input type="submit" class="c-btn3"   value="<?php echo $this->_var['lang']['add_bonus']; ?>" />
      </form>
      </section>
    </section>
    <?php endif; ?> 
     
     
    <?php if ($this->_var['action'] == 'order_list'): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['label_order']; ?> </h1>
    </header>
    <section class="wrap order_list">
      <section class="order_box padd1 radius10 single_item">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
      	<tr>
        	<td class="order_status" style="border-bottom:1px #CCCCCC dashed"></td>
        </tr>
        <tr>
        	<td class="order_content"></td>
        </tr>
        <tr>
          <td class="order_handler"></td>
        </tr>
        <tr style="display:none;">
            <td class="order_tracking"></td>
        </tr>
      </table>
    </section>
    <a href="javascript:;" style="text-align:center;" class="get_more"></a>
    </section>
	<script type="text/javascript" src="<?php echo $this->_var['ectouch_themes']; ?>/js/jquery.more.js"></script>
    <script type="text/javascript">
    <?php $_from = $this->_var['lang']['merge_order_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
	    var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    jQuery(function($){
    	$('.order_list').more({'address': 'user.php?act=async_order_list', amount: 5, 'spinner_code':'<div style="text-align:center; margin:10px;"><img src="<?php echo $this->_var['ectouch_themes']; ?>/images/loader.gif" /></div>'});
		$(window).scroll(function () {
			if ($(window).scrollTop() == $(document).height() - $(window).height()) {
				$('.get_more').click();
			}
		});
    });
    </script>
    <?php endif; ?> 
     
     
    <?php if ($this->_var['action'] == 'track_packages'): ?>
    <header id="header">
      <div class="c-inav">
        <section>
          <button class="back">
          <span><em></em></span><a href="javascript:history.go(-1)">返回</a>
          </button>
        </section>
        <section> <span style="font-size:14px; color:#333; font-weight:normal"><?php echo $this->_var['lang']['label_track_packages']; ?></span> </section>
        <section></section>
      </div>
    </header>
    <div class="fullscreen">
      <div class="blank"></div>
      <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#dddddd" id="order_table">
        <tr align="center">
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['order_number']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['handle']; ?></td>
        </tr>
        <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <tr>
          <td align="center" bgcolor="#ffffff"><a href="user.php?act=order_detail&order_id=<?php echo $this->_var['item']['order_id']; ?>"><?php echo $this->_var['item']['order_sn']; ?></a></td>
          <td align="center" bgcolor="#ffffff"><?php echo $this->_var['item']['query_link']; ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </table>
      <script>
      var query_status = '<?php echo $this->_var['lang']['query_status']; ?>';
      var ot = document.getElementById('order_table');
      for (var i = 1; i < ot.rows.length; i++)
      {
        var row = ot.rows[i];
        var cel = row.cells[1];
        cel.getElementsByTagName('a').item(0).innerHTML = query_status;
      }
      </script>
      <div class="blank5"></div>
      <?php echo $this->fetch('library/pages.lbi'); ?> </div>
    <?php endif; ?> 
    

     
    <?php if ($this->_var['action'] == 'order_tracking'): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="javascript:history.go(-1)"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['label_track_packages']; ?> </h1>
    </header>
    <div class="fullscreen">
      <div class="blank"></div>
      <div data-role="content" class="smart-result">
        <div class="content-primary">
          <table id="queryResult" cellpadding="0" cellspacing="0"></table>
        </div>
      </div>
<script type="text/javascript">
jQuery(function($){
	var resultJson = eval('(<?php echo $this->_var['trackinfo']; ?>)');
	var resultTable = $("#queryResult");
	resultTable.empty();
	if(resultJson.status == 200) { //成功
		var resultData = resultJson.data;
		for (var i = resultData.length - 1; i >= 0; i--) {
			var className = "";
			if(i%2 == 0){
				className = "even";
			}else{
				className="odd";
			}
			if(resultData.length == 1){
				if(resultJson.ischeck == 1){
					className += " checked";
				}else{
					className += " wait";
				}
			}else if(i == resultData.length - 1){
				className += " first-line";
			}else if(i == 0){
				className += " last-line";
				if(resultJson.ischeck == 1){
					className += " checked";
				}else{
					className += " wait";
				}
			}

			var index = resultData[i].ftime.indexOf(" ");
			var result_date = resultData[i].ftime.substring(0,index);
			var result_time = resultData[i].ftime.substring(index+1);

			var s_index = result_time.lastIndexOf(":");
			result_time = result_time.substring(0,s_index);

			resultTable.append("<tr class='" + className + "'><td class='col1'><span class='result-date'>" + result_date + "</span><span class='result-time'>" + result_time + "</span></td><td class='colstatus'></td><td class='col2'><span>" + resultData[i].context + "</span></td></tr>");
		}
		$("body").animate({scrollTop: "1000px"}, 1000);
	}else if(resultJson.status == 400){
		resultTable.append("<tr><td>订单暂未发货，请稍后再来查询</td></tr>");				
	}else{
		resultTable.append("<tr><td>"+ resultJson.message +"</td></tr>");				
	}
})
</script>
    </div>
    <?php endif; ?> 
     
    
     
    <?php if ($this->_var['action'] == order_detail): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php?act=order_detail&order_id=<?php echo $this->_var['order_id']; ?>"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['order_status']; ?> </h1>
    </header>
    <section class="wrap">
      <section class="order_box padd1 radius10">
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
      	<tr>
        	<td>订单状态：<?php echo $this->_var['order']['order_status']; ?> <?php echo $this->_var['order']['pay_status']; ?> <?php echo $this->_var['order']['shipping_time']; ?></td>
        </tr>
        <tr>
        	<td>订单编号：<?php echo $this->_var['order']['order_sn']; ?></td>
        </tr>
        <tr>
        	<td>订单金额：<?php echo $this->_var['order']['formated_total_fee']; ?></td>
        </tr>
        <tr>
        	<td>下单时间：<?php echo $this->_var['order']['formated_add_time']; ?></td>
        </tr>
        <?php if ($this->_var['order']['to_buyer']): ?>
        <tr>
          <td><?php echo $this->_var['lang']['detail_to_buyer']; ?>：<?php echo $this->_var['order']['to_buyer']; ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($this->_var['virtual_card']): ?>
        <tr>
          <td><?php echo $this->_var['lang']['virtual_card_info']; ?>：<br>
          	<?php $_from = $this->_var['virtual_card']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'vgoods');if (count($_from)):
    foreach ($_from AS $this->_var['vgoods']):
?> 
            <?php $_from = $this->_var['vgoods']['info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'card');if (count($_from)):
    foreach ($_from AS $this->_var['card']):
?> 
            <hr style="border:none;border-top:1px #CCCCCC dashed; margin:5px 0" />
            <?php if ($this->_var['card']['card_sn']): ?><?php echo $this->_var['lang']['card_sn']; ?>:<span style="color:red;"><?php echo $this->_var['card']['card_sn']; ?></span><br><?php endif; ?>
            <?php if ($this->_var['card']['card_password']): ?><?php echo $this->_var['lang']['card_password']; ?>:<span style="color:red;"><?php echo $this->_var['card']['card_password']; ?></span><br><?php endif; ?> 
            <?php if ($this->_var['card']['end_date']): ?><?php echo $this->_var['lang']['end_date']; ?>:<?php echo $this->_var['card']['end_date']; ?><br><?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($this->_var['order']['invoice_no']): ?>
        <tr style="display:none;">
            <td><a href="user.php?act=order_tracking&order_id=<?php echo $this->_var['order']['order_id']; ?>" class="c-btn3">订单跟踪</a></td>
        </tr>
        <?php endif; ?>
      </table>
      </section>
      
      <section class="order_box padd1 radius10">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
        <tr>
          <td width="32%" align="right"><?php echo $this->_var['lang']['consignee_name']; ?>：</td>
          <td width="68%" align="left"><?php echo $this->_var['order']['consignee']; ?></td>
        </tr>
        <?php if ($this->_var['order']['exist_real_goods']): ?>
        <tr>
          <td align="right"><?php echo $this->_var['lang']['detailed_address']; ?>：</td>
          <td align="left"><?php echo $this->_var['order']['address']; ?> 
            <?php if ($this->_var['order']['zipcode']): ?> 
            [<?php echo $this->_var['lang']['postalcode']; ?>: <?php echo $this->_var['order']['zipcode']; ?>] 
            <?php endif; ?></td>
        </tr>
        <?php endif; ?>
        <tr>
          <td align="right">联系<?php echo $this->_var['lang']['phone']; ?>：</td>
          <td align="left"><?php echo $this->_var['order']['tel']; ?> </td>
        </tr>
        
      </table>
      </section>
      
      <section class="order_box padd1 radius10">
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
      	  <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
          <tr>
            <td rowspan="2" width="60" align="center" valign="middle" style="border-bottom:1px #CCCCCC dashed">
            <a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank"><?php if ($this->_var['goods']['goods_thumb']): ?><img src="<?php echo $this->_var['site_url']; ?><?php echo $this->_var['goods']['goods_thumb']; ?>" width="60" height="60" /><?php endif; ?></a></td>
            <td><?php echo $this->_var['goods']['goods_name']; ?></td>
          </tr>
          <tr>
            <td style="border-bottom:1px #CCCCCC dashed">售价：<?php echo $this->_var['goods']['goods_price']; ?> 数量：<?php echo $this->_var['goods']['goods_number']; ?><br>小计：<?php echo $this->_var['goods']['subtotal']; ?> 型号：<?php echo $this->_var['goods']['goods_attr']; ?></td>
          </tr>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </table>
      <?php if ($this->_var['formated_order_amount'] != "0.00"): ?>
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
        <tr>
          <td align="right"><?php echo $this->_var['lang']['order_amount']; ?>: <?php echo $this->_var['order']['formated_order_amount']; ?> 
            <?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><br />
            <?php echo $this->_var['lang']['notice_gb_order_amount']; ?><?php endif; ?></td>
        </tr>
        <?php if ($this->_var['pay_id'] == 3): ?>
        <?php if ($this->_var['pay_status'] != 2): ?>
        <tr>
          <td align="right" bgcolor="#ffffff">
           <a href="flow.php?step=done&order_id=<?php echo $this->_var['order_id']; ?>" class="c-btn3">微信支付</a>
          </td>
        </tr>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->_var['pay_id'] == 4): ?>
        
        <?php if ($this->_var['order']['pay_image'] != ''): ?>
        	<a href="<?php echo $this->_var['order']['pay_image']; ?>"><img src="<?php echo $this->_var['order']['pay_image']; ?>" width="100" height="80"/></a>
        <?php else: ?>
        <form name="pingzheng" method="post" action="user.php" enctype="multipart/form-data">
        	<label>支付凭证：</label><input type="file" name="pay_image" value=''/>
        	<input type="hidden" name="old_order_id" value="<?php echo $this->_var['order']['order_id']; ?>" />
        	<input type="hidden" name="act" value="payment_img" />
        	<input type="submit" value="上传" />
        </form>
        <?php endif; ?>
        
        <tr>
          <td align="right" bgcolor="#ffffff">
            <a href="#" class="c-btn3">请到pc端使用支付宝支付</a>
          </td>
        </tr>
        <?php endif; ?>
        <?php if ($this->_var['allow_edit_surplus']): ?>
        <!-- <tr>
          <td align="right" bgcolor="#ffffff"><form action="user.php" method="post" name="formFee" id="formFee">
              <?php echo $this->_var['lang']['use_more_surplus']; ?>：
              <input name="surplus" type="text" size="8" value="0" style="border:1px solid #ccc; padding:3px; border-radius:5px;"/><br>
              <?php echo $this->_var['max_surplus']; ?>
              <input type="submit" name="Submit" class="c-btn3" value="<?php echo $this->_var['lang']['button_submit']; ?>余额付款" />
              <input type="hidden" name="act" value="act_edit_surplus" />
              <input type="hidden" name="order_id" value="<?php echo $_GET['order_id']; ?>" />
            </form></td>
        </tr> -->
        <?php endif; ?>
      </table>
      <?php endif; ?>
      </section>
      <?php if ($this->_var['order']['pay_status'] == '已付款'): ?>
      <section class="order_box padd1 radius10">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">      
        <tr class="wuliu">
          <th align="left">型号</th>          
          <th align="left">产地</th>          
          <th align="left">数量</th>
          <th align="left">发货地</th> 
          <th align="left">城市</th> 
          <th align="left">门店</th>           
        </tr>
        <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
        <tr>
          <td bgcolor="#ffffff"><?php echo $this->_var['goods']['goods_attr']; ?></td>
       
        <td bgcolor="#ffffff"><?php echo $this->_var['goods']['pro_is']; ?></td>
       
          <td bgcolor="#ffffff"><?php echo $this->_var['goods']['goods_number']; ?></td>
        <?php if ($this->_var['goods']['status'] == "1"): ?>  
          <td bgcolor="#ffffff">未发货</td>
        <?php else: ?> 
          <td bgcolor="#ffffff"><?php echo $this->_var['goods']['send_time']; ?></td>  
        <?php endif; ?>  
        <?php if ($this->_var['goods']['status'] >= "3"): ?> 
          <td bgcolor="#ffffff"><?php echo $this->_var['goods']['fw_time']; ?></td>
        <?php else: ?> 
          <td bgcolor="#ffffff"></td>  
        <?php endif; ?>   
        <?php if ($this->_var['goods']['status'] >= "4"): ?> 
          <td bgcolor="#ffffff"><?php echo $this->_var['goods']['jx_time']; ?></td>
        <?php else: ?> 
          <td bgcolor="#ffffff"></td>  
        <?php endif; ?>      
        </tr> 
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>      
      </table>
      </section>
      <?php endif; ?>
      <section class="order_box padd1 radius10" style="display:none;">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
        
        <?php if ($this->_var['order']['insure_fee'] > 0): ?> 
        <?php endif; ?> 
        <?php if ($this->_var['order']['pack_name']): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['use_pack']; ?>：</td>
          <td colspan="3" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['pack_name']; ?></td>
        </tr>
        <?php endif; ?> 
        <?php if ($this->_var['order']['card_name']): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['use_card']; ?>：</td>
          <td colspan="3" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['card_name']; ?></td>
        </tr>
        <?php endif; ?> 
        <?php if ($this->_var['order']['card_message']): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['bless_note']; ?>：</td>
          <td colspan="3" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['card_message']; ?></td>
        </tr>
        <?php endif; ?> 
        <?php if ($this->_var['order']['surplus'] > 0): ?> 
        <?php endif; ?> 
        <?php if ($this->_var['order']['integral'] > 0): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['use_integral']; ?>：</td>
          <td colspan="3" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['integral']; ?></td>
        </tr>
        <?php endif; ?> 
        <?php if ($this->_var['order']['bonus'] > 0): ?> 
        <?php endif; ?> 
        <?php if ($this->_var['order']['inv_payee'] && $this->_var['order']['inv_content']): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['invoice_title']; ?>：</td>
          <td width="36%" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['inv_payee']; ?></td>
          <td width="19%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['invoice_content']; ?>：</td>
          <td width="25%" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['inv_content']; ?></td>
        </tr>
        <?php endif; ?> 
        <?php if ($this->_var['order']['postscript']): ?>
        <tr>
          <td width="15%" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['order_postscript']; ?>：</td>
          <td colspan="3" align="left" bgcolor="#ffffff"><?php echo $this->_var['order']['postscript']; ?></td>
        </tr>
        <?php endif; ?>
      </table>
      </section>
      
    </section>
    <?php endif; ?> 
    
     
     
    <?php if ($this->_var['action'] == "account_raply" || $this->_var['action'] == "account_log" || $this->_var['action'] == "account_deposit" || $this->_var['action'] == "account_detail"): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['user_balance']; ?> </h1>
    </header>
    
    <div class="fullscreen"> 
      <script type="text/javascript">
          <?php $_from = $this->_var['lang']['account_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
            var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </script>
      <div class="blank"></div>
      <?php endif; ?> 
      <?php if ($this->_var['action'] == "account_raply"): ?>
      <form name="formSurplus" method="post" action="user.php" onSubmit="return submitSurplus()">
        <table width="100%" border="0" cellpadding="5" cellspacing="1" >
          <tr>
            <td bgcolor="" align="left"><input style="margin-top: 10px;"  placeholder="<?php echo $this->_var['lang']['repay_money']; ?>" type="text" name="amount" value="<?php echo htmlspecialchars($this->_var['order']['amount']); ?>" class="inputBg" size="30" /></td>
          </tr>
          <tr>
            <td bgcolor="" align="left">

            <!--<textarea style="margin-top: 10px;border: none;width: 100%;" placeholder="<?php echo $this->_var['lang']['process_notic']; ?>" name="user_note" cols="55" rows="6"  class="B_blue" ><?php echo htmlspecialchars($this->_var['order']['user_note']); ?></textarea>-->
           <textarea style="margin-top: 10px;border: none;width: 100%;" placeholder="<?php echo $this->_var['lang']['process_notic']; ?>" name="user_note" cols="55" rows="6"  class="B_blue" ><?php if ($this->_var['bank_info']['card_no'] == 0 || $this->_var['bank_info']['card_name'] == '' || $this->_var['bank_info']['bank_name'] == ''): ?>
开户人：XXX;
银行帐号：622XXXXXXXXXXXXX;
开户银行：XX银行XX区XX支行；
            <?php else: ?>
开户人：<?php echo $this->_var['bank_info']['card_name']; ?>;
银行帐号：<?php echo $this->_var['bank_info']['card_no']; ?>;
开户银行：<?php echo $this->_var['bank_info']['bank_name']; ?>;
            <?php endif; ?>
            </textarea>
            </td>
          </tr>
          <tr>
            <td bgcolor="" colspan="2" align="center"><input type="hidden" name="surplus_type" value="1" />
              <input type="hidden" name="act" value="act_account" />
              <input type="submit" name="submit"  class="c-btn" style="margin-top:5px;color: #fff;
    border: 0;
    height: 2.5rem;
    line-height: 2.5rem;
    width: 100%;
    -webkit-box-flex: 1;
    display: block;
    -webkit-user-select: none;
    font-size: 0.9rem;
    background: #0f0;
    text-align: center;" value="<?php echo $this->_var['lang']['submit_request']; ?>" />
          </tr>
        </table>
      </form>
      <?php endif; ?> 
      <?php if ($this->_var['action'] == "account_deposit"): ?>
      <form name="formSurplus" method="post" action="user.php" onSubmit="return submitSurplus()">
        <table width="100%" border="0" cellpadding="5" cellspacing="1" >
          <tr>
            <td align="left" ><input placeholder="<?php echo $this->_var['lang']['deposit_money']; ?>" type="text" name="amount"  class="inputBg" value="<?php echo htmlspecialchars($this->_var['order']['amount']); ?>" size="30" /></td>
          </tr>
          <tr>
            <td align="left" ><textarea  placeholder="<?php echo $this->_var['lang']['process_notic']; ?>" name="user_note" cols="55" rows="6"  class="B_blue"><?php echo htmlspecialchars($this->_var['order']['user_note']); ?></textarea></td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="5" cellspacing="1" >
          <tr align="center">
            <td  colspan="3" align="left"><?php echo $this->_var['lang']['payment']; ?>:</td>
          </tr>
          <tr align="center">
            <td ><?php echo $this->_var['lang']['pay_name']; ?></td>
            <td  width="60%"><?php echo $this->_var['lang']['pay_desc']; ?></td>
            <td  width="17%"><?php echo $this->_var['lang']['pay_fee']; ?></td>
          </tr>
          <?php $_from = $this->_var['payment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
          <tr>
            <td  align="left"><input type="radio" name="payment_id" value="<?php echo $this->_var['list']['pay_id']; ?>" />
              <?php echo $this->_var['list']['pay_name']; ?></td>
            <td  align="left"><?php echo $this->_var['list']['pay_desc']; ?></td>
            <td  align="center"><?php echo $this->_var['list']['pay_fee']; ?></td>
          </tr>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          <tr>
            <td colspan="3"  align="center"><input type="hidden" name="surplus_type" value="0" />
              <input type="hidden" name="rec_id" value="<?php echo $this->_var['order']['id']; ?>" />
              <input type="hidden" name="act" value="act_account" />
              <input type="submit" class="c-btn" style="margin-right:5px;" name="submit" value="<?php echo $this->_var['lang']['submit_request']; ?>" />
              <input type="reset"  class="c-btn"  name="reset" value="<?php echo $this->_var['lang']['button_reset']; ?>" /></td>
          </tr>
        </table>
      </form>
      <?php endif; ?> 
      <?php if ($this->_var['action'] == "act_account"): ?>
      <table width="100%" border="0" cellpadding="5" cellspacing="1" >
        <tr>
          <td width="25%" align="right" ><?php echo $this->_var['lang']['surplus_amount']; ?></td>
          <td width="80%" bgcolor="#ffffff"><?php echo $this->_var['amount']; ?></td>
        </tr>
        <tr>
          <td align="right" ><?php echo $this->_var['lang']['payment_name']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['payment']['pay_name']; ?></td>
        </tr>
        <tr>
          <td align="right" ><?php echo $this->_var['lang']['payment_fee']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['pay_fee']; ?></td>
        </tr>
        <tr>
          <td align="right" valign="middle" ><?php echo $this->_var['lang']['payment_desc']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['payment']['pay_desc']; ?></td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#ffffff"><?php echo $this->_var['payment']['pay_button']; ?></td>
        </tr>
      </table>
      <?php endif; ?> 
      <?php if ($this->_var['action'] == "account_detail"): ?>
      <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#dddddd">
        <tr align="center">
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['process_time']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['surplus_pro_type']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['money']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['change_desc']; ?></td>
        </tr>
        <?php $_from = $this->_var['account_log']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <tr>
          <td align="center" bgcolor="#ffffff"><?php echo $this->_var['item']['change_time']; ?></td>
          <td align="center" bgcolor="#ffffff"><?php echo $this->_var['item']['type']; ?></td>
          <td align="right" bgcolor="#ffffff"><?php echo $this->_var['item']['amount']; ?></td>
          <td bgcolor="#ffffff" title="<?php echo $this->_var['item']['change_desc']; ?>">&nbsp;&nbsp;<?php echo $this->_var['item']['short_change_desc']; ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr>
          <td colspan="4" align="center" bgcolor="#ffffff"><div align="right"><?php echo $this->_var['lang']['current_surplus']; ?><?php echo $this->_var['surplus_amount']; ?></div></td>
        </tr>
      </table>
      <?php echo $this->fetch('library/pages.lbi'); ?> 
      <?php endif; ?> 
      <?php if ($this->_var['action'] == "account_log"): ?>
      <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#dddddd">
        <tr align="center">
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['process_time']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['surplus_pro_type']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['money']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['process_notic']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['admin_notic']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['is_paid']; ?></td>
          <td bgcolor="#ffffff"><?php echo $this->_var['lang']['handle']; ?></td>
        </tr>
        <?php $_from = $this->_var['account_log']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <tr>
          <td align="center" bgcolor="#ffffff"><?php echo $this->_var['item']['add_time']; ?></td>
          <td align="left" bgcolor="#ffffff"><?php echo $this->_var['item']['type']; ?></td>
          <td align="right" bgcolor="#ffffff"><?php echo $this->_var['item']['amount']; ?></td>
          <td align="left" bgcolor="#ffffff"><?php echo $this->_var['item']['short_user_note']; ?></td>
          <td align="left" bgcolor="#ffffff"><?php echo $this->_var['item']['short_admin_note']; ?></td>
          <td align="center" bgcolor="#ffffff"><?php echo $this->_var['item']['pay_status']; ?></td>
          <td align="right" bgcolor="#ffffff"><?php echo $this->_var['item']['handle']; ?> 
            <?php if (( $this->_var['item']['is_paid'] == 0 && $this->_var['item']['process_type'] == 1 ) || $this->_var['item']['handle']): ?> 
            <a href="user.php?act=cancel&id=<?php echo $this->_var['item']['id']; ?>" onclick="if (!confirm('<?php echo $this->_var['lang']['confirm_remove_account']; ?>')) return false;"><?php echo $this->_var['lang']['is_cancel']; ?></a> 
            <?php endif; ?></td>
        </tr>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <tr>
          <td colspan="7" align="right" bgcolor="#ffffff"><?php echo $this->_var['lang']['current_surplus']; ?><?php echo $this->_var['surplus_amount']; ?></td>
        </tr>
      </table>
      <?php echo $this->fetch('library/pages.lbi'); ?> </div>
    <?php endif; ?> 
     
     
    <?php if ($this->_var['action'] == 'address_list'): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="user.php"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['consignee_info']; ?> </h1>
    </header>
    <section class="wrap">
      <?php $_from = $this->_var['consignee_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('sn', 'consignee');if (count($_from)):
    foreach ($_from AS $this->_var['sn'] => $this->_var['consignee']):
?>
      <section class="order_box padd1 radius10">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
        <tr>
          <td width="32%" align="right"><?php echo $this->_var['lang']['consignee_name']; ?>：</td>
          <td width="68%" align="left"><?php echo htmlspecialchars($this->_var['consignee']['consignee']); ?></td>
        </tr>
        <tr>
          <td align="right"><?php echo $this->_var['lang']['phone']; ?>：</td>
          <td align="left"><?php echo $this->_var['consignee']['tel']; ?> </td>
        </tr>
        <tr>
          <td align="right"><?php echo $this->_var['lang']['detailed_address']; ?>：</td>
          <td align="left"><?php echo $this->_var['consignee']['province_1']; ?>-<?php echo $this->_var['consignee']['city_1']; ?>-<?php echo $this->_var['consignee']['district_1']; ?>-<?php echo htmlspecialchars($this->_var['consignee']['address']); ?> 
            <?php if ($this->_var['consignee']['zipcode']): ?> 
            [<?php echo $this->_var['lang']['postalcode']; ?>: <?php echo htmlspecialchars($this->_var['consignee']['zipcode']); ?>] 
            <?php endif; ?></td>
        </tr>
        <tr style="display:none;">
          <td align="right"><?php echo $this->_var['lang']['email_address']; ?>：</td>
          <td align="left"><?php echo htmlspecialchars($this->_var['consignee']['email']); ?></td>
        </tr>
        <tr>
          <td colspan="2" align="right"><!--<a href="user.php?act=act_edit_address&id=<?php echo $this->_var['consignee']['address_id']; ?>&flag=display">编辑</a> | --><a href="user.php?act=drop_consignee&id=<?php echo $this->_var['consignee']['address_id']; ?>" onClick="return confirm('您真的要删除该地址吗？');">删除</a></td>
        </tr>
      </table>
      </section>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
     <!-- <a href="user.php?act=act_edit_address&flag=display" class="c-btn3"><?php echo $this->_var['lang']['add_address']; ?></a>-->
    </section>
    <?php endif; ?> 
     
     
    <?php if ($this->_var['action'] == 'act_edit_address'): ?>
    <header id="header">
      <div class="header_l header_return"> <a class="ico_10" href="javascript:history.go(-1)"> 返回 </a> </div>
      <h1> <?php echo $this->_var['lang']['consignee_info']; ?> </h1>
    </header>
      
      <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,transport.js,region.js,shopping_flow.js')); ?> 
      <script type="text/javascript">
		  region.isAdmin = false;
		  <?php $_from = $this->_var['lang']['flow_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
		  var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
		  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		  
		  onload = function() {
			if (!document.all)
			{
			  document.forms['theForm'].reset();
			}
		  }
		  
	  </script>
    <section class="wrap">
      <section class="order_box padd1 radius10">
      <form action="user.php" method="post" name="theForm" onsubmit="return checkConsignee(this)">
        <table width="100%" border="0" cellpadding="5" cellspacing="0" class="ectouch_table">
          <tr>
          	<td width="70">收货人</td>
            <td align="left" ><input name="consignee" placeholder="<?php echo $this->_var['lang']['consignee_name']; ?><?php echo $this->_var['lang']['require_field']; ?>" type="text" class="inputBg_touch" value="<?php echo htmlspecialchars($this->_var['consignee']['consignee']); ?>" /> </td>
          </tr>
          <tr>
          	<td>联系电话</td>
            <td align="left" ><input placeholder="<?php echo $this->_var['lang']['phone']; ?><?php echo $this->_var['lang']['require_field']; ?>" name="tel" type="text" class="inputBg_touch" value="<?php echo htmlspecialchars($this->_var['consignee']['tel']); ?>" /></td>
          </tr>
          <tr style="display:none;">
          	<td>电子邮箱</td>
            <td align="left" ><input name="email" placeholder="<?php echo $this->_var['lang']['email_address']; ?><?php echo $this->_var['lang']['require_field']; ?>" type="text" class="inputBg_touch" value="test@qq.com" /></td>
          </tr>
          <tr>
          	<td><?php echo $this->_var['lang']['country_province']; ?></td>
            <td align="left" >
               <!--<select name="country" onchange="region.changed(this, 1, 'selProvinces')">
                <option value="0"><?php echo $this->_var['lang']['please_select']; ?><?php echo $this->_var['name_of_region']['0']; ?></option>
                <?php $_from = $this->_var['country_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'country');if (count($_from)):
    foreach ($_from AS $this->_var['country']):
?>
                <option value="<?php echo $this->_var['country']['region_id']; ?>" <?php if ($this->_var['consignee']['country'] == $this->_var['country']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['country']['region_name']; ?></option>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </select>-->
              <select name="province" id="selProvinces" onchange="region.changed(this, 2, 'selCities')">
                <option value="0"><?php echo $this->_var['lang']['please_select']; ?><?php echo $this->_var['name_of_region']['1']; ?></option>
                <?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'province');if (count($_from)):
    foreach ($_from AS $this->_var['province']):
?>
                <option value="<?php echo $this->_var['province']['region_id']; ?>" <?php if ($this->_var['consignee']['province'] == $this->_var['province']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['province']['region_name']; ?></option>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </select>
              <select name="city" id="selCities" onchange="region.changed(this, 3, 'selDistricts')">
                <option value="0"><?php echo $this->_var['lang']['please_select']; ?><?php echo $this->_var['name_of_region']['2']; ?></option>
                <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
                <option value="<?php echo $this->_var['city']['region_id']; ?>" <?php if ($this->_var['consignee']['city'] == $this->_var['city']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['city']['region_name']; ?></option>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </select>
              <select name="district" id="selDistricts" <?php if (! $this->_var['district_list']): ?>style="display:none"<?php endif; ?>>
                <option value="0"><?php echo $this->_var['lang']['please_select']; ?><?php echo $this->_var['name_of_region']['3']; ?></option>
                <?php $_from = $this->_var['district_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'district');if (count($_from)):
    foreach ($_from AS $this->_var['district']):
?>
                <option value="<?php echo $this->_var['district']['region_id']; ?>" <?php if ($this->_var['consignee']['district'] == $this->_var['district']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['district']['region_name']; ?></option>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
              </select></td>
          </tr>
          <tr>
          	<td>详细地址</td>
            <td align="left" ><input name="address" placeholder="<?php echo $this->_var['lang']['detailed_address']; ?><?php echo $this->_var['lang']['require_field']; ?>" type="text" class="inputBg_touch" value="<?php echo htmlspecialchars($this->_var['consignee']['address']); ?>" /></td>
          </tr>
          <tr style="display:none;">
          	<td>邮政编码</td>
            <td align="left" ><input placeholder="<?php echo $this->_var['lang']['postalcode']; ?>" name="zipcode" type="text" class="inputBg_touch" value="<?php echo htmlspecialchars($this->_var['consignee']['zipcode']); ?>" /></td>
          </tr>
          <tr>
            <td align="center" colspan="2"><?php if ($this->_var['consignee']['consignee'] && $this->_var['consignee']['email']): ?>
              <input type="submit" name="submit"  class="c-btn3" value="<?php echo $this->_var['lang']['confirm_edit']; ?>"  style="margin-right:5px;" />
              <?php else: ?>
              <input type="submit" name="submit"  class="c-btn3"  value="<?php echo $this->_var['lang']['add_address']; ?>"/>
              <?php endif; ?>
              <input type="hidden" name="act" value="act_edit_address" />
              <input name="address_id" type="hidden" value="<?php echo $this->_var['consignee']['address_id']; ?>" /></td>
          </tr>
        </table>
      </form>
      </section>
    </section>
    <?php endif; ?> 
     
     
    <?php if ($this->_var['action'] == 'transform_points'): ?>
    <h5><span><?php echo $this->_var['lang']['transform_points']; ?></span></h5>
    <div class="blank"></div>
    <?php if ($this->_var['exchange_type'] == 'ucenter'): ?>
    <form action="user.php" method="post" name="transForm" onsubmit="return calcredit();">
      <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#dddddd">
        <tr>
          <th width="120" bgcolor="#FFFFFF" align="right" valign="top"><?php echo $this->_var['lang']['cur_points']; ?>:</th>
          <td bgcolor="#FFFFFF"><label for="pay_points"><?php echo $this->_var['lang']['exchange_points']['1']; ?>:</label>
            <input type="text" size="15" id="pay_points" name="pay_points" value="<?php echo $this->_var['shop_points']['pay_points']; ?>" style="border:0;border-bottom:1px solid #DADADA;" readonly />
            <br />
            <div class="blank"></div>
            <label for="rank_points"><?php echo $this->_var['lang']['exchange_points']['0']; ?>:</label>
            <input type="text" size="15" id="rank_points" name="rank_points" value="<?php echo $this->_var['shop_points']['rank_points']; ?>" style="border:0;border-bottom:1px solid #DADADA;" readonly /></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <th align="right" bgcolor="#FFFFFF"><label for="amount"><?php echo $this->_var['lang']['exchange_amount']; ?>:</label></th>
          <td bgcolor="#FFFFFF"><input size="15" name="amount" id="amount" value="0" onkeyup="calcredit();" type="text" />
            <select name="fromcredits" id="fromcredits" onchange="calcredit();">
              
                  <?php echo $this->html_options(array('options'=>$this->_var['lang']['exchange_points'],'selected'=>$this->_var['selected_org'])); ?>
                
            </select></td>
        </tr>
        <tr>
          <th align="right" bgcolor="#FFFFFF"><label for="desamount"><?php echo $this->_var['lang']['exchange_desamount']; ?>:</label></th>
          <td bgcolor="#FFFFFF"><input type="text" name="desamount" id="desamount" disabled="disabled" value="0" size="15" />
            <select name="tocredits" id="tocredits" onchange="calcredit();">
              
                <?php echo $this->html_options(array('options'=>$this->_var['to_credits_options'],'selected'=>$this->_var['selected_dst'])); ?>
              
            </select></td>
        </tr>
        <tr>
          <th align="right" bgcolor="#FFFFFF"><?php echo $this->_var['lang']['exchange_ratio']; ?>:</th>
          <td bgcolor="#FFFFFF">1 <span id="orgcreditunit"><?php echo $this->_var['orgcreditunit']; ?></span> <span id="orgcredittitle"><?php echo $this->_var['orgcredittitle']; ?></span> <?php echo $this->_var['lang']['exchange_action']; ?> <span id="descreditamount"><?php echo $this->_var['descreditamount']; ?></span> <span id="descreditunit"><?php echo $this->_var['descreditunit']; ?></span> <span id="descredittitle"><?php echo $this->_var['descredittitle']; ?></span></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF"><input type="hidden" name="act" value="act_transform_ucenter_points" />
            <input type="submit" name="transfrom" value="<?php echo $this->_var['lang']['transform']; ?>" /></td>
        </tr>
      </table>
    </form>
    <script type="text/javascript">
        <?php $_from = $this->_var['lang']['exchange_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'lang_js');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['lang_js']):
?>
        var <?php echo $this->_var['key']; ?> = '<?php echo $this->_var['lang_js']; ?>';
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

        var out_exchange_allow = new Array();
        <?php $_from = $this->_var['out_exchange_allow']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'ratio');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['ratio']):
?>
        out_exchange_allow['<?php echo $this->_var['key']; ?>'] = '<?php echo $this->_var['ratio']; ?>';
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

        function calcredit()
        {
            var frm = document.forms['transForm'];
            var src_credit = frm.fromcredits.value;
            var dest_credit = frm.tocredits.value;
            var in_credit = frm.amount.value;
            var org_title = frm.fromcredits[frm.fromcredits.selectedIndex].innerHTML;
            var dst_title = frm.tocredits[frm.tocredits.selectedIndex].innerHTML;
            var radio = 0;
            var shop_points = ['rank_points', 'pay_points'];
            if (parseFloat(in_credit) > parseFloat(document.getElementById(shop_points[src_credit]).value))
            {
                alert(balance.replace('{%s}', org_title));
                frm.amount.value = frm.desamount.value = 0;
                return false;
            }
            if (typeof(out_exchange_allow[dest_credit+'|'+src_credit]) == 'string')
            {
                radio = (1 / parseFloat(out_exchange_allow[dest_credit+'|'+src_credit])).toFixed(2);
            }
            document.getElementById('orgcredittitle').innerHTML = org_title;
            document.getElementById('descreditamount').innerHTML = radio;
            document.getElementById('descredittitle').innerHTML = dst_title;
            if (in_credit > 0)
            {
                if (typeof(out_exchange_allow[dest_credit+'|'+src_credit]) == 'string')
                {
                    frm.desamount.value = Math.floor(parseFloat(in_credit) / parseFloat(out_exchange_allow[dest_credit+'|'+src_credit]));
                    frm.transfrom.disabled = false;
                    return true;
                }
                else
                {
                    frm.desamount.value = deny;
                    frm.transfrom.disabled = true;
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
       </script> 
    <?php else: ?> 
    <b><?php echo $this->_var['lang']['cur_points']; ?>:</b>
    <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#dddddd">
      <tr>
        <td width="30%" valign="top" bgcolor="#FFFFFF"><table border="0">
            <?php $_from = $this->_var['bbs_points']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'points');if (count($_from)):
    foreach ($_from AS $this->_var['points']):
?>
            <tr>
              <th><?php echo $this->_var['points']['title']; ?>:</th>
              <td width="120" style="border-bottom:1px solid #DADADA;"><?php echo $this->_var['points']['value']; ?></td>
            </tr>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </table></td>
        <td width="50%" valign="top" bgcolor="#FFFFFF"><table>
            <tr>
              <th><?php echo $this->_var['lang']['pay_points']; ?>:</th>
              <td width="120" style="border-bottom:1px solid #DADADA;"><?php echo $this->_var['shop_points']['pay_points']; ?></td>
            </tr>
            <tr>
              <th><?php echo $this->_var['lang']['rank_points']; ?>:</th>
              <td width="120" style="border-bottom:1px solid #DADADA;"><?php echo $this->_var['shop_points']['rank_points']; ?></td>
            </tr>
          </table></td>
      </tr>
    </table>
    <br />
    <b><?php echo $this->_var['lang']['rule_list']; ?>:</b>
    <ul class="point clearfix">
      <?php $_from = $this->_var['rule_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'rule');if (count($_from)):
    foreach ($_from AS $this->_var['rule']):
?>
      <li><font class="f1">·</font>"<?php echo $this->_var['rule']['from']; ?>" <?php echo $this->_var['lang']['transform']; ?> "<?php echo $this->_var['rule']['to']; ?>" <?php echo $this->_var['lang']['rate_is']; ?> <?php echo $this->_var['rule']['rate']; ?> 
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <form action="user.php" method="post" name="theForm">
      <table width="100%" border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse:collapse;border:1px solid #DADADA;">
        <tr style="background:#F1F1F1;">
          <th><?php echo $this->_var['lang']['rule']; ?></th>
          <th><?php echo $this->_var['lang']['transform_num']; ?></th>
          <th><?php echo $this->_var['lang']['transform_result']; ?></th>
        </tr>
        <tr>
          <td><select name="rule_index" onchange="changeRule()">
              <?php $_from = $this->_var['rule_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'rule');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['rule']):
?>
              <option value="<?php echo $this->_var['key']; ?>"><?php echo $this->_var['rule']['from']; ?>-><?php echo $this->_var['rule']['to']; ?></option>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </select></td>
          <td><input type="text" name="num" value="0" onkeyup="calPoints()"/></td>
          <td><span id="ECS_RESULT">0</span></td>
        </tr>
        <tr>
          <td colspan="3" align="center"><input type="hidden" name="act" value="act_transform_points"  />
            <input type="submit" value="<?php echo $this->_var['lang']['transform']; ?>" /></td>
        </tr>
      </table>
    </form>
    <script type="text/javascript">
          //<![CDATA[
            var rule_list = new Object();
            var invalid_input = '<?php echo $this->_var['lang']['invalid_input']; ?>';
            <?php $_from = $this->_var['rule_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'rule');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['rule']):
?>
            rule_list['<?php echo $this->_var['key']; ?>'] = '<?php echo $this->_var['rule']['rate']; ?>';
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            function calPoints()
            {
              var frm = document.forms['theForm'];
              var rule_index = frm.elements['rule_index'].value;
              var num = parseInt(frm.elements['num'].value);
              var rate = rule_list[rule_index];

              if (isNaN(num) || num < 0 || num != frm.elements['num'].value)
              {
                document.getElementById('ECS_RESULT').innerHTML = invalid_input;
                rerutn;
              }
              var arr = rate.split(':');
              var from = parseInt(arr[0]);
              var to = parseInt(arr[1]);

              if (from <=0 || to <=0)
              {
                from = 1;
                to = 0;
              }
              document.getElementById('ECS_RESULT').innerHTML = parseInt(num * to / from);
            }

            function changeRule()
            {
              document.forms['theForm'].elements['num'].value = 0;
              document.getElementById('ECS_RESULT').innerHTML = 0;
            }
          //]]>
       </script> 
    <?php endif; ?> 
    <?php endif; ?> 
     
    
</div>
<div style="width:1px; height:1px; overflow:hidden"><?php $_from = $this->_var['lang']['p_y']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pv');if (count($_from)):
    foreach ($_from AS $this->_var['pv']):
?><?php echo $this->_var['pv']; ?><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?></div>
</body>
<script type="text/javascript">
    
    function del(){
              //删除用户信息
              $.ajax({
                type:'GET',
                url :'user.php',
                data:{'act':'del_profile'},
                dataType: "json",
                success: function(data){
                    alert('删除成功');
                    
                }
            })   
    }
    function ajax_store(id){
          if(id!='0'){
                $.get("./region.php?act=add_exe&id="+id,function(res){
                  $(".stor_list").html(res);
                }); 
              }else{
                $(".stor_list").html('');
              }
            }
   function modify(){
           var mod=document.getElementById("modify");
           mod.style.display="";
   }
</script>

</html>
