<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>



<?php

//连接数据库
$con = mysql_connect("localhost","******","******");
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
	
		echo "<script> alert('您的激活码有效期已过，请重新申请发送激活邮件.');location.href='http://forum.baystgroup.ca/identityverify/'; </script>"; //过期提示消息
		
	}else{  //没有过期
	
	mysql_query("UPDATE pre_common_member SET extgroupids=concat(extgroupids,'	','$identity') where uid='$uid'");//修改数据库数据
	$rc = mysql_affected_rows();
if($rc>0){  //成功
	mysql_query("UPDATE `pre_common_member_profile` SET verifitoken=null,verifitoken_exptime=null,verifitime=null, identity=null,temporaryemail=null WHERE `uid`= '$uid'");//清空临时数据库
	mysql_query("update pre_common_member set verify_email=concat(verify_email,'$email') where uid='$uid'");//更新用户邮箱

	echo "<script> alert('恭喜您，验证成功');location.href='http://forum.baystgroup.ca/forum.php'; </script>"; //弹出提示消息
    
    } echo "<script> alert('验证失败，请联系网站管理员.');location.href='http://forum.baystgroup.ca/forum.php?mod=forumdisplay&fid=64'; </script>"; //验证失败

	}
}else{  //其他情况
	echo "<script> alert('验证失败，请联系网站管理员.');location.href='http://forum.baystgroup.ca/forum.php?mod=forumdisplay&fid=64'; </script>";	
}

mysql_close($con);

?>
