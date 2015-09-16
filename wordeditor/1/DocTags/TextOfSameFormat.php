<?php
require_once 'Row.php';
require_once 'Cell.php';
class TextOfSameFormat{
	private $_text;
	private $_fStyle;
	private $_isBreak=false;//判断是否是一个换行符
	private $_isTable=false;//判断是否是一个表格
	private $_rows=array();//表格的行
	private $_styleTable=array();//表格属性
	private $_rowNow=null;
	private $_isSection=false;
	private $_isImg=false;
	private $_imgSrc='';//图片路径
	private $_imgStyle='';//图片属性
	public function __construct(){
		
		
	}
	public function setImg($src='',$imgStyle=array()){
		$this->_isImg=true;
		$this->_imgSrc=$src;
		$this->_imgStyle=$imgStyle;

	}
	public function isImg(){
		return $this->_isImg;
	}
	public function getImgSrc(){
		return $this->_imgSrc;
	}
	public function getImgStyle(){
		return $this->_imgStyle;
	}
	public function setSection(){
		$this->_isSection=true;
	}
	public function isSection(){
		return $this->_isSection;
	}
	public function isTable(){
		return $this->_isTable;
	}
	public function setTable($styleTable=array()){
		$this->_isTable=true;
		$this->_styleTable=$styleTable;
	}
	public function getTableStyle(){
		return $this->_styleTable;

	}
	public function addRow(){
		$this->_rowNow=new Row();
		array_push($this->_rows,$this->_rowNow);
	}
	public function getRows(){
		return $this->_rows;
	}
	public function addCell($style=array()){
		$this->_rowNow->addCell($style);
	}
	public function addCellText($text='',$fStyle=array()){
		$this->_rowNow->addCellText($text,$fStyle);
	}
	public function cellAddBreak(){
		$this->_rowNow->cellAddBreak();
	}
	public function setText($text=''){
		$this->_text=$text;

	}
	public function addParagraphInCell(){
		$this->_rowNow->addParagraphInCell();
	}
	public function addPStyleInCell($key='',$value=''){
		$this->_rowNow->addPStyleInCell($key,$value);
	}
	public function addBreakToParaInCell(){
		$this->_rowNow->addBreakToParaInCell();
	}
	public function addTextToParaInCell($text='',$fStyle=array()){
		$this->_rowNow->addTextToParaInCell($text,$fStyle);
	}
	public function setFStyle($fStyle=array()){
		$this->_fStyle=$fStyle;
	}
	public function getText(){
		return $this->_text;
	}
	public function getFStyle(){
		return $this->_fStyle;
	}
	public function setBreak(){
		$this->_isBreak=true;
	}
	public function isBreak(){
		return $this->_isBreak;
	}
	public function printSelf(){
		echo($this->_text);
		echo('=>');
		foreach($this->_fStyle as $key=>$value){
			echo($key);
			echo(':');
			echo($value);
			echo(';');
		}
	}
}
?>