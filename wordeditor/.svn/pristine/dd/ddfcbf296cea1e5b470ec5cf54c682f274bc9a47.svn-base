<?php
require_once 'Paragraph.php';
require_once 'TextOfSameFormat.php';
class Cell{
	private $_cellStyle=array();
	private $_text=array();
	private $_isParagraphs=false;//cell中的内容有可能是几段文本（包含p标签），有可能只是一段文本（不包含p标签）
	private $_paragraphs=array();
	private $_paragraphNow=null;
	public function __construct($style=array()){
		$this->_cellStyle=$style;
	}
	public function getStyle(){
		return $this->_cellStyle;
	}
	public function addText($text='',$fStyle=array()){
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setText($text);
		$textOfSameFormat->setFStyle($fStyle);
		array_push($this->_text,$textOfSameFormat);
	}
	public function addTextBreak(){
		$textOfSameFormat=new TextOfSameFormat();
		$textOfSameFormat->setBreak();
		array_push($this->_text,$textOfSameFormat);
	}
	public function getText(){
		return $this->_text;
	}
	public function isParagraphs(){
		return $this->_isParagraphs;
	}
	public function addParagraph(){
		$this->_isParagraphs=true;
		$this->_paragraphNow=new Paragraph();
		array_push($this->_paragraphs,$this->_paragraphNow);
	}
	public function addPStyle($key='',$value=''){
		$this->_paragraphNow->addPStyle($key,$value);
	}
	public function addBreakToPara(){
		$this->_paragraphNow->addBreak();
	}
	public function addTextToPara($text='',$fStyle=array()){
		$this->_paragraphNow->addText($text,$fStyle);
	}
	public function getParagraphs(){
		return $this->_paragraphs;
	}


}
?>