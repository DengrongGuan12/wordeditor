<?php
require_once "Cell.php";
class Row{
	private $_cells=array();
	private $_cellNow=null;
	public function __construct(){
	}
	public function addCell($style=array()){
		$this->_cellNow=new Cell($style);
		array_push($this->_cells,$this->_cellNow);
		
	}
	public function getCells(){
		return $this->_cells;
	}
	public function addCellText($text='',$fStyle=array()){
		$this->_cellNow->addText($text,$fStyle);
	}
	public function cellAddBreak(){
		$this->_cellNow->addTextBreak();
	}
	public function addParagraphInCell(){
		$this->_cellNow->addParagraph();
	}
	public function addPStyleInCell($key='',$value=''){
		$this->_cellNow->addPStyle($key,$value);
	}
	public function addBreakToParaInCell(){
		$this->_cellNow->addBreakToPara();
	}
	public function addTextToParaInCell($text='',$fStyle=array()){
		$this->_cellNow->addTextToPara($text,$fStyle);
	}
}
?>