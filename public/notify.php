<?php
/**
 * 支付推送服务消息接收文件
 */
require( '../../../../wp-load.php' );//此路径位于根目录

/* Payjs支付通知开始 */
$data = payjs_notify();//获取payjs支付成功的信息


$money = $data['total_fee']/100; 	//交易金额
$userid = $data['attach']; 	//交易标题,积分充值是用户ID,付费可见的时候是文章ID
$orderid = $data['out_trade_no']; //订单ID

error_log('Payjs pay ok, Order_ID:'.$orderid.', Order_Title:'.$userid.', Money:'.$money);//输出到日志


if(in_string($userid,'PP')){//免登陆支付,插入缓存,然后直接停止推出
	set_transient($userid, $orderid, 30);
	exit;
}

if(gdk_check($data['out_trade_no']) !== 0) exit('Repeat push');//在入库前,数据库不应该有同样的订单号

if( empty($userid) || empty($money) ) exit('data null');//阻止某些极少数空值的


/* Payjs支付通知结束 */

$user = get_user_by( 'id', $userid  );
$point_number = $money * gdk_option('gdk_rate');
$headers = "Content-Type:text/html;charset=UTF-8\n";
$mail_title = '尊敬的'.$user->display_name.'，您好！';
$mail_cotent = '<p>您的金币充值已成功到账，请查收！</p><p>金币充值金额为:'.$user->display_name.'</p><p>如果您的金币金额有异常，请您在第一时间和我们取得联系哦，联系邮箱：'.get_bloginfo('admin_email').'</p>';
$message = mail_temp($mail_title,$mail_cotent,home_url(),get_bloginfo('name'));
GDK_Points::set_points($point_number, $userid, array('description' => $orderid , 'status' => 'accepted'));
//增加金币金币	
wp_mail( $user->user_email , 'Hi,'.$user->display_name.'，充值成功到账通知！', $message, $headers);
$mail_admin_cotent = '<p>充值订单</p><p>用户ID：'.$userid.'</p><p>用户名：'.$user->display_name.'</p><p>金额'.$money.'元</p>';
$admin_notice = mail_temp('站长你好',$mail_admin_cotent,home_url(),get_bloginfo('name'));
wp_mail( get_bloginfo('admin_email') , '【收款成功】网站充值订单已完成',$admin_notice, $headers);
//more

?>