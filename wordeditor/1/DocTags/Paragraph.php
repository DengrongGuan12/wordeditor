<?php
require_once 'TextOfSameFormat.php';
//顺序记录一个段落中的内容及其对应的格式
//有可能只是一个换行符
class Paragraph{
	private $_pStyle=array();//记录该段落的格式
	private $_paragrWithDiffStyle=array();
	private $_tableNow=null;//当前正在处理的表格
	private $_isSection=false;//是否是创建一个新的页面（即一个分页符）
	private $_isTitle=false;//是否是一个标题
	private $_titleLevel=0;//标题级别
	private $_titleText='';
	public function __construct(){
	}
	public function setSection(){
		$this->_isSection=true;
	}
	public function isSection(){
		return $this->_isSection;
	}
	public function addImg($src='',$imgStyle=array()){
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setImg($src,$imgStyle);
		array_push($this->_paragrWithDiffStyle,$textOfSameFormat);
	}
	public function addSection(){
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setSection();
		array_push($this->_paragrWithDiffStyle,$textOfSameFormat);
	}
	public function setTitleLevel($level=0){
		$this->_isTitle=true;
		$this->_titleLevel=$level;
	}
	public function setTitleText($text=''){
		$this->_titleText=$text;
	}
	public function isTitle(){
		return $this->_isTitle;
	}
	public function getTitleLevel(){
		return $this->_titleLevel;
	}
	public function getTitleText(){
		return $this->_titleText;
	}
	public function addPStyle($key='',$value=''){
		$this->_pStyle=array_merge($this->_pStyle,array($key=>$value));
	}
	public function addText($text='',$fStyle=array()){
		//array_merge($this->_paragrWithDiffStyle,$text=>);
		//array_push($this->_paragrWithDiffStyle,$text=>$fStyle);
		//$this->_paragrWithDiffStyle[$text]=$fStyle;
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setText($text);
		$textOfSameFormat->setFStyle($fStyle);
		array_push($this->_paragrWithDiffStyle,$textOfSameFormat);
	}
	public function printSelf(){
		if($this->isBreak()){
			echo('break');
			echo('<br />');
		}else{
			foreach($this->_paragrWithDiffStyle as $textOfSameFormat){
				$textOfSameFormat->printSelf();
				echo('<br />');

			}
		}
		
	}
	public function getParagrWithDiffStyle(){
		return $this->_paragrWithDiffStyle;
	}
	public function getPStyle(){
		return $this->_pStyle;
	}
	public function addBreak(){
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setBreak();
		array_push($this->_paragrWithDiffStyle,$textOfSameFormat);
	}
	public function addTable($styleTable=array()){
		echo('add a table!!'.'<br />');
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setTable($styleTable);
		$this->_tableNow=$textOfSameFormat;
		array_push($this->_paragrWithDiffStyle,$this->_tableNow);

	}
	public function tableAddRow(){
		$this->_tableNow->addRow();
	}
	public function addCell($style=array()){
		$this->_tableNow->addCell($style);
	}
	public function addCellText($text='',$fStyle=array()){
		$this->_tableNow->addCellText($text,$fStyle);

	}
	public function cellAddBreak(){
		$this->_tableNow->cellAddBreak();
	}
	public function addParagraphInCell(){
		$this->_tableNow->addParagraphInCell();
	}
	public function addPStyleInCell($key='',$value=''){
		$this->_tableNow->addPStyleInCell($key,$value);
	}
	public function addTextToParaInCell($text='',$fStyle=array()){
		$this->_tableNow->addTextToParaInCell($text,$fStyle);
	}
	public function addBreakToParaInCell(){
		$this->_tableNow->addBreakToParaInCell();
	}
}
?>