<?php
/**
* 	配置账号信息
*/

class WxPayConfig
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 * 
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 * 
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 * 
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * 
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
    //===================联充充电  开始=======================
//    const APPID = 'wxfecc98603e75b1d3';
//    const MCHID = '1300336101';
//    const KEY  = 'uNiCharge20161qaz2wsxEDCunicharg';
//    const APPSECRET = 'e2fb6c102f3f4d183e3380ae5357da41';
    //===================联充充电  结束=======================
     
     //===================充电无忧  开始=======================
//	const APPID = 'wx4935b433ff06a61e';
//	const MCHID = '1277651401';
//	const KEY  = 'uNiCharge12343edc2wsxQAZunichage';
//	const APPSECRET = '7b76cd393935101fc3596b1379160664';
    //===================充电无忧  结束=======================
    
    //===================联充公众号  开始=======================
    const APPID = WXAPPID;
    const MCHID = WXMCHID;
    const KEY  = WXKEY;
    const APPSECRET = WXNOTIFY_URL;
    //===================联充公众号  结束=======================
	
	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const SSLCERT_PATH = '../cert/apiclient_cert.pem';// sample
	const SSLKEY_PATH = '../cert/apiclient_key.pem';// sample
	
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";//"10.10.193.10";test02的代理地址
	const CURL_PROXY_PORT = 0;//3128;test02的代理端口
	
	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;
}
