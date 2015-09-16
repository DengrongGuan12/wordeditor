var Try={
		these:function(){
			var returnValue;
			for(var i=0,length=arguments.length;i<length;i++){
				var lambda = arguments[i];
				try{ 
					returnValue=lambda();
					break;
				}catch(e){
					
				}
			}
			return returnValue;
		}
};
var Ajax={
		getTransport:function(){
			return Try.these(
				function(){return new XMLHttpRequest()},
				function(){return new ActiveXObject('Msxml2.XMLHTTP')},
				function(){return new ActiveXobject('Microsoft.XMLHTTP')}
			)
		}
};
var XMLHttp={
		_xmlhttpCache:[],
		_getXmlhttp:function(){
			for(var i=0;i<this._xmlhttpCache.length;i++){
				if(this._xmlhttpCache[i].readyState==0||this._xmlhttpCache[i].readyState==4){
					return this._xmlhttpCache[i];
				}
			}
			this._xmlhttpCache[this._xmlhttpCache.length]=Ajax.getTransport();
			return this._xmlhttpCache[this._xmlhttpCache.length-1];
		},
		send:function(method,url,data,callback){
			var xmlhttp=this._getXmlhttp();
			with(xmlhttp){
				try {
					if(url.indexOf("?")!=-1){
						url+="&requestTime="+(new Date()).getTime();
					}else{
						url+="?requestTime="+(new Date()).getTime();
					}
					open(method,url,true);
					setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=UTF-8');
					send(data);
					onreadystatechange=function(){
						if(xmlhttp.readyState==4 && (xmlhttp.status==200||xmlhttp.status==304)){
							callback(xmlhttp);
						}
					}
					
				} catch (e) {
					// TODO: handle exception
				}
			}
			
			
		}
}
//检查图片是否存在  
function CheckImgExists(imgurl) {  
    var ImgObj = new Image(); //判断图片是否存在  
    ImgObj.src = imgurl;  
    //没有图片，则返回-1  
    if (ImgObj.fileSize > 0 || (ImgObj.width > 0 && ImgObj.height > 0)) {  
        return true;  
    } else {  
        return false;  
    }  
}  