<?php

include 'ai/AipNlp.php'; //�ı������ӿ�
//include 'ai/AipContentCensor.php'; //������˽ӿ�





function gdk_newsSummary($post_ID){

// ��� APPID AK SK
$APP_ID = '22837593';
$API_KEY = 'hYYE9TWp0T5Um5b0p1FUALlw';
$SECRET_KEY = 'otXMPD6TGGWKjKnfm9aB9USErsQrH2HD';

$Nlpclient = new AipNlp($APP_ID, $API_KEY, $SECRET_KEY);
$post = get_post( $post_ID );
	$content = '�ӹ������濴ͳ�Ų���ϵͳ��Ŀǰ��Ϊ�����汾����һ����ͳ�Ų���ϵͳרҵ�棬Ȼ����ͳ�Ų���ϵͳ���˰棬Ȼ����ͳ�Ų���ϵͳ�����棬Ҳ�������ڵ����ϵͳ��������Ҫע��һ�㣬�������ϵͳ�����Ѿ��ǳ���֪���ȣ������ں���Ҳ��һ����֪���ȣ�����ͳ�Ų���ϵͳ������û�и�������Ȼʹ�����ϵͳ���֡�
�������汾�����У�������רҵ�����ṩ���������Ȼ���ʹ�ã����˰���Ը�����ʹ�õģ�Ȼ��������͸��˰��࣬����Ȩ��Ҫ�ȸ��˰�Ҫ����Ҳ������һ�㣬����רҵ������Ҫ���ѵģ�Ȼ����˰�������ֵ����ģ�Ȼ������������ȫ��ѵġ�
��������һ��Ҫ˵��һ�¸��˰���û��rootȨ�޵ģ������ҪrootȨ�ޣ���Ҫ��ͨ������Ȩ�ޣ�������Ȩ����Ҫע��ģ�Ȼ����������Ĭ���Դ�rootȨ�޵ġ�û��root��Ȩ�ޣ����ںܶ�linuxϵͳ��������˵�ǲ�̫�Ѻõģ����Ƕ��ڸ����û���˵�����ȴ�Ƿǳ���Ҫ�ģ���Ϊ�Դ�rootȨ�޶�һЩ�Ե��Բ�̫��Ϥ������˵ʵ����̫Σ���ˣ������װѵ��Ի���ϵͳ�㻵�ġ�';


$maxSummaryLen = 300;

// ��������������ժҪ�ӿ�
$result = $Nlpclient->newsSummary($content, $maxSummaryLen);

var_dump($result);


$my_post = array(
	'ID' => $post_ID,
'post_excerpt' => $result //ժҪ��Ϣ

);


//���

//wp_insert_post( $my_post );

}
add_action('publish_post', 'gdk_newsSummary', 0);
add_action('update_post', 'gdk_newsSummary', 0);