<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>



<?php

//连接数据库
$con = mysql_connect("localhost","********","*******");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  
  }

$verify = stripslashes(trim($_GET['verify'])); //token
$identity = stripslashes(trim($_GET['identity'])); //用户组id

$nowtime = time(); //现在时间

mysql_select_db("baystree_forum", $con); //选择数据库

//获取用户id
$result2 = mysql_query("select * from pre_common_member_profile where `verifitoken`='$verify'");
$row2 = mysql_fetch_array($result2);
$uid = $row2['uid'];
//结束获取
//获取用户认证邮箱
$email = $row3['temporaryemail'];
//结束获取

//验证信息
$query = mysql_query("select verifitoken_exptime from pre_common_member_profile where `verifitoken`='$verify'") or die(mysql_error()) ;  //获取过期时间
$row = mysql_fetch_assoc($query);//得到数据
if($row){         //如果有数据存在
	if($nowtime>$row['verifitoken_exptime']){ //30min,如果已经过期
		$msg = '您的激活有效期已过，请登录您的帐号重新发送激活邮件.';
	}else{  //没有过期
	
	mysql_query("UPDATE pre_common_member SET extgroupids=concat(extgroupids,'	','$identity') where uid='$uid'");//修改数据库数据
	$rc = mysql_affected_rows();
if($rc>0){  //成功
mysql_query("UPDATE `pre_common_member_profile` SET verifitoken=null,verifitoken_exptime=null,verifitime=null, identity=null,temporaryemail=null WHERE `uid`= '$uid'");//清空临时数据库
mysql_query("update pre_common_member set verify_email=concat(verify_email,'$email') where uid='$uid'");//更新用户邮箱
    $msg = '验证成功';
    } else {$msg = '验证失败，请联系网站管理员';} //验证失败

	}
}else{  //其他情况
	$msg = '未知错误，请联系网站管理员';	
}

echo $msg;
mysql_close($con);

?>