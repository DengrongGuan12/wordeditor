<?php
require_once 'MyWord.php';
require_once 'DocTags/Paragraph.php';
class WordGenerator{
	private $_myWord;
	public function __construct(){
		$this->_myWord=new MyWord();

	}
	public function generateParagraphs($paragraphs){
		$this->_myWord->createSection();

		foreach($paragraphs as $paragraph){
			if($paragraph->isSection()){
				echo('create section!!! in WordGenerator on line 15<br />');
				$this->_myWord->createSection();
			}else if($paragraph->isTitle()){
				$level=$paragraph->getTitleLevel();
				$text=$paragraph->getTitleText();
				$this->_myWord->addTitle($text,$level);
			}else{
				$this->_myWord->addParagrWithDiffStyle($paragraph->getParagrWithDiffStyle(),$paragraph->getPStyle());
			}
			
			
		}

	}
	public function generate(){
		$this->_myWord->save('docxs/lexerTest');
	}

}
?>