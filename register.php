<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>


<?php
//连接数据库
$con = mysql_connect("localhost","*******","******");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

$username = stripslashes(trim($_POST['username']));//获取用户名

//检测用户名是否存在
mysql_select_db("baystree_forum", $con);

$sql = "SELECT * FROM `pre_common_member` WHERE `username`='$username'";

$result = mysql_query($sql);

$num=mysql_num_rows($result);

if($num<1){
    echo '<script>alert("对不起，您输入的用户名不存在，请输入正确用户名。或注册");window.history.go(-1);</script>';
    exit;
  }
//结束验证 
//获取用户uid
while($row = mysql_fetch_array($result))
  {
  $uidgot = $row['uid'];
 
  }
//结束获取

//验证之前是否有未完成的验证
//$sql2 = "SELECT `identity` FROM `pre_common_member_profile` WHERE `uid`=$uidgot";
//$result2 = mysql_query($sql2);
//验证完成

//生成Email
$emailname = trim($_POST['email']);
$emailadd = trim($_POST['emailadd']);

$abc=array($emailname,"@",$emailadd); 
$email=implode("",$abc);

//结束生成email
//获取用户需要申请的认证
$identity = trim($_POST['identity']);

//结束获取
$regtime = time();
$safepin = "baystreet";

$token = md5($username.$regtime); //创建用于激活识别码

$token_exptime = time()+60*60*24;//过期时间为24小时后

$sql1 = "UPDATE pre_common_member_profile SET temporaryemail='$email', identity='$identity', verifitoken='$token',verifitoken_exptime='$token_exptime', verifitime='$regtime' where uid='$uidgot'";

mysql_query($sql1);

if(mysql_affected_rows()){//写入成功，发邮件
	$to = "$email";    
	$subject = "贝街论坛验证地址";
	$from = "forum@baystreetbbs.org";
	$message = "亲爱的".$username."：感谢您使用贝街论坛。请点击链接验证您的邮箱。<a href='http://forum.baystgroup.ca/identityverify/active.php?verify=".$token."&identity=".$identity."' target='_blank'> http://forum.baystgroup.ca/identityverify/active.php?verify=".$token."&identity=".$identity."如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。如果此次激活请求非你本人所发，请忽略本邮件。";
	$rs = mail($to,$subject,$message);
	if($rs==1){
		echo '<script>alert("邮件已发送，请尽快登陆到您的邮箱验证");window.history.go(-1);</script>';	
	}
 }
mysql_close($con);
?>
