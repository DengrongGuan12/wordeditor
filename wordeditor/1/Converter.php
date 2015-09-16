<?php
class Converter{
	private $_htmlToWord=array('strong'=>'bold','em'=>'italic','u'=>'underline','sub'=>'subScript','sup'=>'superScript',
		'border'=>'borderSize','bordercolor'=>'borderColor','align'=>'alignMent','cellpadding'=>'cellMargin');//定义html标签和word之间的对应关系
	private $_colorMap=array('#ffff00'=>'yellow','#00ff00'=>'green','#00ffff'=>'cyan','#ff00ff'=>'magenta','#0000ff'=>'blue','#ff0000'=>'red',
		'#000080'=>'darkBlue','#008080'=>'darkCyan','#008000'=>'darkGreen','#800080'=>'darkMagenta','#800000'=>'darkRed','#808000'=>'darkYellow',
		'#808080'=>'darkGray','#c0c0c0'=>'lightGray','#000000'=>'black');//定义十六进制到颜色的映射

	private $_fontSize=array('8'=>6,'9'=>7,'10'=>7.5,'11'=>8,'12'=>9,'13'=>10,'14'=>10.5,'15'=>11,'16'=>12,'17'=>13,'18'=>13.5,'19'=>14,'20'=>14.5,'21'=>15,'22'=>16,'23'=>17,'24'=>18,'26'=>20,'29'=>22,'32'=>24,'35'=>26,'36'=>27,'37'=>28,'38'=>29,'40'=>30,'42'=>32,'45'=>34,'48'=>36,'56'=>44);

	public function convertHtmlToWord($tag=''){
		return $this->_htmlToWord[$tag];

	}
	public function convertSize($size=''){
		//将带有px的size转换成word中的size
		$size=explode('p',$size);
		return $this->_fontSize[$size[0]];


	}
	public function convertToFgColor($backColor=''){
		//word中只有几种前景色，所以要转换就要取与其颜色最相近的颜色
		$backColor_int=hexdec($backColor);
		$interval=PHP_INT_MAX;
		$finalKey='#ffff00';
		foreach(array_keys($this->_colorMap) as $key){
			$temp=abs($backColor_int - hexdec($key));
			if($temp<=$interval){
				$interval=$temp;
				$finalKey=$key;
			}
		}
		return $this->_colorMap[$finalKey];

	}

}
?>