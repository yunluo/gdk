<?php 


$t_url = preg_replace('/^url=(.*)$/i','$1',$_SERVER["QUERY_STRING"]);
if(!empty($t_url)) {
	preg_match('/(http|https):\/\//',$t_url,$matches);
	if($matches){
		$url=$t_url;
		$title='页面加载中,请稍候...';
	} else {
		$title='加载中...';
		echo "<script>setTimeout(function(){window.opener=null;window.close();}, 3000);</script>";
	}
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="refresh" content="1;url='<?php echo $url;?>';">
<title><?php echo $title;?></title>
<style type="text/css">
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline
}

body {
	background: #3498db;
}

#loader-container {
	width: 188px;
	height: 188px;
	color: white;
	margin: 0 auto;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
	border: 5px solid #3498db;
	border-radius: 50%;
	-webkit-animation: borderScale 1s infinite ease-in-out;
	animation: borderScale 1s infinite ease-in-out;
}

#loadingText {
	font-family: 'Raleway', sans-serif;
	font-size: 1.4em;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-right: -50%;
	transform: translate(-50%, -50%);
}

@-webkit-keyframes borderScale {
	0% {
		border: 5px solid white;
	}

	50% {
		border: 25px solid #3498db;
	}

	100% {
		border: 5px solid white;
	}
}

@keyframes borderScale {
	0% {
		border: 5px solid white;
	}

	50% {
		border: 25px solid #3498db;
	}

	100% {
		border: 5px solid white;
	}
}
</style>
</head>
<body>
<div id="loader-container"><p id="loadingText">页面加载中...</p></div>
</body>
</html>