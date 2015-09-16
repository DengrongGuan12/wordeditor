var xmlHttp;
function save(str)
{
if (str.length==0)
 {
 alert("请输入内容！");
 return
 }
//获取xmlHttpObject对象，如果为空，提示浏览器不支持ajax
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
 //获取url
var url="lexer_test.php";
var postStr   = "html="+ str;

 //回调函数，执行动作
xmlHttp.onreadystatechange=stateChanged;
 //open
xmlHttp.open("POST",url,true);
xmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
xmlHttp.send(postStr);
} 
//获取xml对象
function GetXmlHttpObject()
{
var xmlHttp=null;
try
{
// Firefox, Opera 8.0+, Safari
xmlHttp=new XMLHttpRequest();
}
catch (e)
{
// Internet Explorer
try
 {
 xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
 }
catch (e)
 {
 xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
 }
}
return xmlHttp;
}
function stateChanged()
{
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
{
window.open("docxs/lexerTest.docx");
}
}