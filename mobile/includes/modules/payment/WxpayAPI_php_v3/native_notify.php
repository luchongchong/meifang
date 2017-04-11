<?php
//require(dirname(__FILE__) . '/wechat.class.php');
//require(ROOT_PATH . 'weixin/index.php');
//初始化日志
//$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);

class NativeNotifyCallBack extends WxPayNotify
{
	/**
	 * 统一下单
	 */
	public function wxunifiedorder($order_info, $notify_url, $open_id)
	{
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($order_info['order_name']);
		$input->SetOut_trade_no($order_info['order_no']);
		$input->SetTotal_fee($order_info['online_pay_sum']);
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type('JSAPI');
		$input->SetAttach(strval($order_info['attach']));
        $input->SetOpenid($open_id);
        $input->SetDevice_info('WEB');
        $input->SetTime_start($order_info['time_start']);
        $input->SetTime_expire($order_info['time_expire']);
        
		$result = WxPayApi::unifiedOrder($input);
		$return_code = $result['return_code'];
		if($return_code === 'FAIL'){
			$return_msg = isset($result['return_msg']) ? $result['return_msg'] : '';
			return false;
		}else{
			$result_code = $result['result_code'];
			if($result_code === 'FAIL'){
				$err_code = isset($result['err_code']) ? $result['err_code'] : '';
				$err_code_des = isset($result['err_code_des']) ? $result['err_code_des'] : '';
				return false;
			}
		}
		//创建APP端预支付参数
		$data['appId'] = WxPayConfig::APPID;
        $data['timeStamp'] = $order_info['current_time'];
        $data['nonceStr'] = WxPayApi::getNonceStr();
        $data['package'] = 'prepay_id='.$result['prepay_id'];
		$data['signType'] = 'MD5';
		
		$data['sign'] = $this->myMakeSign($data); 
		
		return $data;
	}
	
	/**
	 * 生成签名
	 *  @return 签名
	 */
	private function myMakeSign($params){
		//签名步骤一：按字典序排序数组参数
		ksort($params);
		$string = $this->myToUrlParams($params);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".WxPayConfig::KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
	 * 将参数拼接为url: key=value&key=value
	 * @param	$params
	 * @return	string
	 */
	private function myToUrlParams($params){
		$string = '';
		if( !empty($params) ){
			$array = array();
			foreach( $params as $key => $value ){
				$array[] = $key.'='.$value;
			}
			$string = implode("&",$array);
		}
		return $string;
	}
	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){

			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);

		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @return string content
	 */
	function http_post($url,$param){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_string($param)) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	static function json_encode($arr) {
		$parts = array ();
		$is_list = false;
		//Find out if the given array is a numerical array;die();
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
				if ($i != $keys [$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) { //Custom handling for arrays
				if ($is_list)
					$parts [] = self::json_encode ( $value ); /* :RECURSION: */
				else
					$parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (is_numeric ( $value ) && $value<2000000000)
					$str .= $value; //Numbers
				elseif ($value === false)
					$str .= 'false'; //The booleans
				elseif ($value === true)
					$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']'; //Return numerical JSON
		return '{' . $json . '}'; //Return associative JSON
	}
	/**
	 *  author:royallu
	 *   func:消息推送
	 *   time:20170309
	 * */
	public function push_message($log_id){
		$sql = 'SELECT order_amount FROM ' . $GLOBALS['ecs']->table('pay_log') ." WHERE log_id = '$log_id'";
		$amount = $GLOBALS['db']->getOne($sql);
		//添加微信公众号通知功能
		$sql_order_id= 'SELECT order_id FROM ' . $GLOBALS['ecs']->table('pay_log') ." WHERE log_id = '$log_id'";
		$order_id = $GLOBALS['db']->getOne($sql_order_id);

		$sql_order_sn= 'SELECT order_sn FROM ' . $GLOBALS['ecs']->table('order_info') ." WHERE order_id = '$order_id'";
		$order_sn = $GLOBALS['db']->getOne($sql_order_sn);
		$str="恭喜家人,您的相关订单(".$order_sn.")共计".$amount."元已经付款成功,请等待发货";


		$user_id  = $GLOBALS['db']->getOne ( "SELECT user_id FROM " . $GLOBALS['ecs']->table('order_info') . " WHERE `order_id` = ".$order_id );
		$own_id_openid = $GLOBALS['db']->getOne ( "SELECT fake_id FROM " . $GLOBALS['ecs']->table('weixin_user') . " WHERE `ecuid` = ".$user_id );

		$weixinconfig = $GLOBALS['db']->getRow ( "SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1" );
		$this->log(ROOT_PATH.'/data/wx_new_log.txt','test444'.'4444444'."\r\n");
		//$weixin = new core_lib_wechat($weixinconfig);
		//$weixin->valid();
		//获取access_toke



		$url_1="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$weixinconfig['appid']."&secret=".$weixinconfig['appsecret'];
		$result=$this->http_get($url_1);
		if($result){
			$json = json_decode($result,true);
		}
		$url_2="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$json['access_token'];

		if($own_id_openid){
			//发送信息
			$data=array(
				"touser"=>$own_id_openid,
				"msgtype"=>"text",
				"text"=>array(
					//"content"=>$weixinconfig['reply_superiors']
					"content"=> $str
				)
			);
			$result2=$this->http_post($url_2,self::json_encode($data));
		}
	

	}


	/**
	 * 微信支付回调
	 */
	public function NotifyProcess($data, &$msg)
	{
	    	$flag=false;
			$log_id_new="";
		$this->log(ROOT_PATH.'/data/wx_new_log.txt',json_encode($data)."\r\n");
		//echo "处理回调";
		if ($data===false){
			// return false,微信会重新回调该方法，总共回调8次。通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒
			return false;
		}else{
			if($data['return_code'] === 'SUCCESS' ){
			        $log_id=$data['attach'];
					order_paid($log_id, 2);
					$log_id_new=$log_id;
					$flag=true;
				}
			if($flag){
				$this->push_message($log_id_new);
			}
			return true;
		}
		
	}
	public function log($file,$txt)
	{
	    $fp =  fopen($file,'ab+');
	    fwrite($fp,'-----------'.local_date('Y-m-d H:i:s').'-----------------');
	    fwrite($fp,$txt);
	    fwrite($fp,"\r\n\r\n\r\n");
	    fclose($fp);
	}
	
}


