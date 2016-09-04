<!DOCTYPE html>
<html>

<head>

<!--表格設定
邊框粗實心，
字體粗大-->
<style>
table, tr, td{
   border: 2px solid black;
   font-size: 120%;
   font-weight: bold;
}
</style>

<!--告訴瀏覽器這個頁面的編碼是UTF-8，
瀏覽器會自動選用正確的編碼-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>

<body>

<h1>
成大數學所研究室座位表-416
</h1>

顯示訊息：
<font color="red" id="showMessageFront">
</font>

<?php
//////////////////////////////
//來定義一下名詞
//資料庫叫db，資料表叫table，資料叫data，欄位叫field，
//['xxx']表示名稱為xxx的這個欄位

//////////////////////////////
//連接db
//別忘了改成你自己的host name還有帳號密碼
//我已經用MyAdmin創建一個名為MyDB的db
//然後在其中創建一個MyTable的table了
//所以你要輸入你自己的db及table名稱
mysql_connect("mysql10.000webhost.com", "a9652871_db", "mysqldbmi4ma3");
mysql_select_db("a9652871_db.416");
mysql_query("set names utf8");

//////////////////////////////
//先取得總有多少筆db
$wholeTable = mysql_query("select * from a9652871_db.416");
$numberOfData = mysql_num_rows($wholeTable);

//////////////////////////////
//先說明一下，
//我的table有五個field
//sn表示序號
//name表示姓名
//spw表示選位密碼
//cpw表示退選密碼
//fix表示這個位置是否固定，例如冰箱，值為1表示固定
//因為data是亂序的
//所以我要將data按照['sn']排序
//並且存入array當中
//這樣等下就可以按照['sn']依序取得這些data
for($i=1; $i<=$numberOfData; $i++)
{
  $row[$i] = mysql_query("select * from a9652871_db.416 where sn=$i");
  $data[$i] = mysql_fetch_assoc($row[$i]);
}

//////////////////////////////
//宣告一些等下要用的GLOBALS變數
//就是空座位數跟已被選取的座位數
$indexOfEmptyButton = 1;
$indexOfSelectedButton = 1;

//////////////////////////////
//show出table中的所有data的field:name
//依序填入一張有四直行的表格（每四筆資料會換下一列）
//我的資料有三種
//一種是fix，fix的值為1
//一種是empty，name的值為''
//一種是selected，name的值非空字串
//會按照資料的類型不同呼叫不同的函數
//作出相應的動作
echo "<table>";
for($j=1; $j<$numberOfData+1; $j++)
{
  if(($j-1)%4==0)
    echo "<tr>";

  if($data[$j]['fix']==1)
    { echo "<td>"; funFixed($j); echo "</td>"; }
  elseif($data[$j]['name']=='')
    { echo "<td>"; funEmpty($j); echo "</td>"; }
  else
    { echo "<td>"; funSelected($j); echo "</td>"; }
  
  if($j%4==0)
  echo "</tr>";
}
echo "</table>"; 

//////////////////////////////
//如果這個座位是固定的，
//也就是['fix']值為1，
//那就只輸出['name']，
//用紅色字體
function funFixed($k)
{
  echo "<font color='red'>";
  echo $GLOBALS['data'][$k]['name'];
  echo "</font><br><br><br><br><br>";
}

//////////////////////////////
//如果這個座位是空的
//也就是['name']==''
//那就產生一些html code，
//用來產生一個輸入姓名，輸入選位密碼，設定退選密碼，選位按鈕的表格
function funEmpty($k)
{
  $temp = $GLOBALS['indexOfEmptyButton'];
  echo "<form action=\"\" method=\"post\">";
  echo "<input type=\"text\" name=\"name$temp\" placeholder=\"&#36664;&#20837;&#22995;&#21517;\"><br>";
  echo "<input type=\"submit\" name=\"selbtn$temp\" value=\"&#36984;&#20301;\"><br>";
  echo "<input type=\"text\" name=\"emSpw$temp\" placeholder=\"&#36664;&#20837;&#36984;&#20301;&#23494;&#30908;\"><br>";
  echo "<input type=\"text\" name=\"emCpw$temp\" placeholder=\"&#35373;&#23450;&#36864;&#36984;&#23494;&#30908;\">";
  echo "</form>";
  $GLOBALS['emMapping'][$GLOBALS['indexOfEmptyButton']] = $k;
  $GLOBALS['indexOfEmptyButton']++;
}

//////////////////////////////

function funSelected($k)
{
  $temp = $GLOBALS['indexOfSelectedButton'];
  echo $GLOBALS['data'][$k]['name'];
  echo "<form action=\"\" method=\"post\">";
  echo "<input type=\"submit\" name=\"cancbtn$temp\" value=\"&#36864;&#36984;\"><br>";
  echo "<input type=\"text\" name=\"seCpw$temp\" placeholder=\"&#36664;&#20837;&#36864;&#36984;&#23494;&#30908;\">";
  echo "</form>";
  echo "<br>";
  $GLOBALS['seMapping'][$GLOBALS['indexOfSelectedButton']] = $k;
  $GLOBALS['indexOfSelectedButton']++;
}

//////////////////////////////

for($l=1; $l<$indexOfSelectedButton; $l++)
{
  if(isset($_POST['cancbtn'.$l]))
  {
    if($data[$seMapping[$l]]['cpw']==$_POST['seCpw'.$l])
    {
      mysql_query("update a9652871_db.416 set name='' where sn='$seMapping[$l]'");
      header("Refresh:0"); 
    }
    echo "<p id ='showMessageBehind'>&#23494;&#30908;&#37679;&#35492;</p>";
  }
}


for($l=1; $l<$indexOfEmptyButton; $l++)
{
  if(isset($_POST['selbtn'.$l]))
  {  
    if($data[$emMapping[$l]]['spw']==$_POST['emSpw'.$l])
    {
      $updateName = $_POST['name'.$l];
      mysql_query("update a9652871_db.416 set name='$updateName' where sn='$emMapping[$l]'");
      $updateNamePW = $_POST['emCpw'.$l];
      mysql_query("update a9652871_db.416 set cpw='$updateNamePW' where sn='$emMapping[$l]'");
      header("Refresh:0");
    }
    echo "<p id ='showMessageBehind'>&#23494;&#30908;&#37679;&#35492;</p>";
  }
}

?>  

<script>
document.getElementById("showMessageFront").innerHTML = document.getElementById("showMessageBehind").innerHTML
</script>


</body>
</html>
