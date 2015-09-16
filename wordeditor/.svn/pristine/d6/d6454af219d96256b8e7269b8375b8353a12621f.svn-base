<?php
require_once 'core/PHPWord/PHPWord.php';
require_once 'DocTags/TextOfSameFormat.php';
require_once 'DocTags/Cell.php';
require_once 'DocTags/Row.php';



class MyWord{
	private $_sectionNow;//当前页面
	private $_sections;
	private $_PHPWord;
	private $_properties;
	private $_sectionCount;//记录当前页数
	private $_footer;
	private $_header;
	private $_levels=array(
		1=>array('name'=>'微软雅黑','size'=>22,'bold'=>true),
		2=>array('name'=>'微软雅黑','size'=>20,'bold'=>true),
		3=>array('name'=>'微软雅黑','size'=>18,'bold'=>true),
		4=>array('name'=>'微软雅黑','size'=>16,'bold'=>true)
		);//标题级别和对应的样式
	private $_levelsCreated=array(
		1=>false,
		2=>false,
		3=>false,
		4=>false
		);//记录标题级别有没有创建
	public function __construct($properties=array()) {
		// New Word Document
		$this->_PHPWord = new PHPWord();
		$this->_properties=$this->_PHPWord->getProperties();

		$this->_sections=array();
		$this->_sectionCount=0;

	}
	//标题级别
	public function addLevel($level=1){
		if(!$this->_levelsCreated[$level]){
			$this->_PHPWord->addTitleStyle($level,$this->_levels[$level]);
			$this->_levelsCreated[$level]=true;
		}
		

	}

	
	//添加标题，参数为标题内容，标题级别
	public function addTitle($text='',$level=1){
		$this->addLevel($level);//添加默认的标题级别
		$this->_sectionNow->addTitle($text,$level);

	}

	//添加目录
	public function addTOC($fontStyle=array()){
		$this->_sectionNow->addTOC($fontStyle);

	}
	public function save($name){
		$objWriter = PHPWord_IOFactory::createWriter($this->_PHPWord, 'Word2007');
		$objWriter->save($name.'.docx');
	}
	//创建封面
	public function createCover($title='文档标题'){
		$this->createSection();
		
		$this->addTextBreak(25);
		$this->_sectionNow->addText($title,array('size'=>18, 'bold'=>true),array('align'=>'center'));

	
	}
	//创建新页面
	public function createSection($sectionStyle=array()){
		
		$this->_sectionNow=$this->_PHPWord->createSection($sectionStyle);
		array_push($this->_sections,$this->_sectionNow);
		
	}
	//在当前页面创建新的文本（另起一行）（不够时自动换页）
	public function addText($text='',$fontStyle=array(),$pStyle=array()){
		$this->_sectionNow->addText($text,$fontStyle,$pStyle);
	}
	public function addTextBreak($breaks=0){
		$this->_sectionNow->addTextBreak($breaks);
	}
	//在当前页面添加图片（不够时自动换页）
	public function addImage($path='',$style=array()){
		$this->_sectionNow->addImage($path,$style);
	}
	//在当前页面添加表格,默认使用一种格式
	public function addTable($textOfSameFormat=null){
		echo('add a table to word!!'.'<br />');
		$this->_PHPWord->addTableStyle('myTable',$textOfSameFormat->getTableStyle(),null);
		//$styleTable = array('borderColor'=>'black',
		//	  'borderSize'=>6,
		//	  'cellMargin'=>50,
		//	  'bgColor'=>'ffffcb',
		//	  'alignMent'=>'center'
		//	);
		//$styleFirstRow = array('bgColor'=>'c3ddff');
		//$this->_PHPWord->addTableStyle('myTable', $styleTable, $styleFirstRow);
		$table = $this->_sectionNow->addTable('myTable');
		//$cellStyle1=array('bgColor'=>'white');
		//$cellStyle2=array('valign'=>'center');
		//$fStyle1=array('color'=>'blue');
		//$pStyle1=array('align'=>'center');
		$cellWidth=6000;
		//$i=0;//判断是奇数行还是偶数行
		//$j=0;//判断是不是第一行，因为第一行的字体不同

		//记录横向合并的次数
		$cellMerge=0;
		//记录纵向合并的列数和对应的次数
		$rowMerge=array();
		$rowMergeStyle=array();
		$rowNum=0;
		foreach($textOfSameFormat->getRows() as $row){
			$table->addRow();
			$columnNum=0;
			foreach($row->getCells() as $cell){
				$columnNum++;
				echo('processing the '.$columnNum.' column<br />');
				if(array_key_exists($columnNum,$rowMerge)){
					if($rowMerge[$columnNum]>1){
						$rowMerge[$columnNum]--;
						$table->addCell($cellWidth,$rowMergeStyle[$columnNum]);
						$columnNum++;

					}
				}
				$cellStyle=$cell->getStyle();
				$tempCellStyle=array();
				$pStyle=array();
				if(array_key_exists('bgColor',$cellStyle)){
					$tempCellStyle['bgColor']=$cellStyle['bgColor'];
				}
				if(array_key_exists('align',$cellStyle)){
					$tempCellStyle['valign']=$cellStyle['align'];
					$pStyle['align']=$cellStyle['align'];
				}
				if(array_key_exists('colspan',$cellStyle)){
					$cellMerge=$cellStyle['colspan'];
					$tempCellStyle['cellMerge']= 'restart';
				}
				if(array_key_exists('rowspan',$cellStyle)){
					$tempCellStyle['rowMerge']='restart';
					$rowMerge[$columnNum]=$cellStyle['rowspan'];
					$tempRowMergeStyle=$tempCellStyle;
					$tempRowMergeStyle['rowMerge']='continue';
					$rowMergeStyle[$columnNum]=$tempRowMergeStyle;

				}
				$realCell=$table->addCell($cellWidth,$tempCellStyle);
				if($cell->isParagraphs()){
					foreach($cell->getParagraphs() as $paragraph){
						$textrun=$realCell->createTextRun($pStyle);
						$paragrWithDiffStyle=$paragraph->getParagrWithDiffStyle();
						$len=count($paragrWithDiffStyle);
						for($i=0;$i<$len;$i++){
							$textOfSameFormat=$paragrWithDiffStyle[$i];
							if($textOfSameFormat->isBreak()){
								if($len==1){
									continue;//只有一个换行符没有其他文本
								}else{
									$textrun=$realCell->createTextRun($pStyle);//如果遇到换行符则新建一个段落
								}
				
							}else{
								$textrun->addText($textOfSameFormat->getText(),$textOfSameFormat->getFStyle());
							}
						}
					}
				}else{
					
					$textrun=$realCell->createTextRun($pStyle);
					$paragrWithDiffStyle=$cell->getText();
					$len=count($paragrWithDiffStyle);
					for($i=0;$i<$len;$i++){
						$textOfSameFormat=$paragrWithDiffStyle[$i];
						if($textOfSameFormat->isBreak()){
							if($len==1){
								continue;//只有一个换行符没有其他文本
							}else{
								$textrun=$realCell->createTextRun($pStyle);//如果遇到换行符则新建一个段落
							}
				
						}else{
							$textrun->addText($textOfSameFormat->getText(),$textOfSameFormat->getFStyle());
						}
					}
				}
				$tempCellStyle['cellMerge']='continue';
				while($cellMerge>1){
					$cellMerge--;
					$columnNum++;
					$table->addCell($cellWidth,$tempCellStyle);
				}
			}
			//最后一列
			$columnNum++;
				if(array_key_exists($columnNum,$rowMerge)){
				
					if($rowMerge[$columnNum]>1){
						echo('the last column!!!!<br />');
						$rowMerge[$columnNum]--;
						echo($rowMergeStyle[$columnNum]['rowMerge']);
						$table->addCell($cellWidth,$rowMergeStyle[$columnNum]);
						$columnNum++;

					}
				}
			
		}

	}

	//添加一段具有不同文字格式的段落
	public function addParagrWithDiffStyle($paragrWithDiffStyle=array(),$pStyle=array()){
		$len=count($paragrWithDiffStyle);
		if($len==1){
			$textOfSameFormat=$paragrWithDiffStyle[0];
			if($textOfSameFormat->getText()==' '){
				$fStyle=$textOfSameFormat->getFStyle();
				$this->_sectionNow->addText('',$fStyle,$pStyle);
				return;
			}
		}
		$textrun = $this->_sectionNow->createTextRun($pStyle);
		$textrun->addText('');
		
		echo('length:'.$len.'<br />');
		for($i=0;$i<$len;$i++){
			$textOfSameFormat=$paragrWithDiffStyle[$i];
			if($textOfSameFormat->isBreak()){
				if($len==1){
					continue;//只有一个换行符没有其他文本
				}else{
					$textrun=$this->_sectionNow->createTextRun($pStyle);//如果遇到换行符则新建一个段落
				}
				
			}else if($textOfSameFormat->isTable()){
				echo('call add a table to word!!'.'<br />');
				$this->addTable($textOfSameFormat);
			}else if($textOfSameFormat->isSection()){
				echo('create section in MyWord on line 222!!!<br />');
				$this->createSection();
				$textrun=$this->_sectionNow->createTextRun($pStyle);
			}else if($textOfSameFormat->isImg()){
				echo("create a image");
				$this->addImage($textOfSameFormat->getImgSrc(),$textOfSameFormat->getImgStyle());
				$textrun=$this->_sectionNow->createTextRun($pStyle);
			}else{
				$textrun->addText($textOfSameFormat->getText(),$textOfSameFormat->getFStyle());
			}
		}

	}




	//横向合并单元格测试
	public function horizontalMerge($title='表格标题',$cells=array()){
		$styleTable = array('borderColor'=>'006699',
			  'borderSize'=>6,
			  'cellMargin'=>50);
		$styleFirstRow = array('bgColor'=>'66BBFF');
		$this->_PHPWord->addTableStyle('myTable', $styleTable, $styleFirstRow);
		$table = $this->_sectionNow->addTable('myTable');
		$table->addRow();
		$table->addCell(2000, array('cellMerge' => 'restart', 'valign' => 'center'))->addText($title);
		$table->addCell(2000, array('cellMerge' => 'continue'))->addText('test');;
		$table->addCell(2000, array('cellMerge' => 'continue'));
		$table->addRow();
		$table->addCell(2000,array('cellMerge'=>'continue'));
		foreach($cells as $row){
			$table->addRow();
			foreach($row as $cell){
				$table->addCell(2000,array('valign'=>'center'))->addText($cell);
			}
		}

	}
	//纵向合并单元格测试
	public function verticalMerge(){
		$styleTable = array('borderColor'=>'006699',
			  'borderSize'=>6,
			  'cellMargin'=>50);
		$styleFirstRow = array('bgColor'=>'006699');
		$this->_PHPWord->addTableStyle('myTable', $styleTable, $styleFirstRow);
		$table = $this->_sectionNow->addTable('myTable');
		$table->addRow();
		$table->addCell(2000,array('rowMerge'=>'restart'))->addText('test(1,1)');
		$table->addCell(2000)->addText('test(1,2)');
		$table->addRow();
		$table->addCell(2000,array('rowMerge'=>'continue'))->addText('test(2,1)');
		$table->addCell(2000)->addText('test(2,2)');
	}

	//在第一页创建页脚
	public function createFooter(){
		//echo('called');

		$this->_footer=$this->_sections[0]->createFooter();
		//$this->_footer->addPreserveText('{PAGE}/{NUMPAGES}', array('align'=>'center'));
	}
	//在第一页创建页眉
	public function  createHeader(){
		$this->_header=$this->_sections[0]->createHeader();
	}
	public function addPageNumToFooter($pStyle=array()){
		$this->_footer->addPreserveText('{PAGE}',null,$pStyle);
		
	}
	//向页脚中添加文字
	public function addTextToFooter($text=''){
		$this->_footer->addText($text);
	}
	//向页眉中添加文字
	public function addTextToHeader($text=''){
		$this->_header->addText($text);
	}
	
	//添加一段话（自动缩进）
	public function addParagraph($text='',$fontStyle=array(),$pStyle=array()){
		$indent='  ';
		$text=$indent.$text;
		$this->_sectionNow->addText($text,$fontStyle,$pStyle);
	}
	public function addParagraphs($paragraphs=array(),$fontStyle=array(),$pStyle=array()){
		foreach($paragraphs as $paragraph){
			$this->addParagraph($paragraph,$fontStyle,$pStyle);
		}
	}
	public function addTexts($texts=array(),$fontStyle=array(),$pStyle=array()){
		foreach($texts as $text){
			$this->addText($text,$fontStyle,$pStyle);
		}

	}
	public function addTitles($titles,$level=1){
		foreach($titles as $title){
			$this->addTitle($title,$level);
		}
	}

	

	//添加多段具有不同文字格式的段落
	public function addMultiParaWithDiffStyle($paragrsWithDiffStyle=array(),$pStyle=array()){
		foreach($paragrsWithDiffStyle as $paragrWithDiffStyle){
			$this->addParagrWithDiffStyle($paragrWithDiffStyle,$pStyle);
		}
	}


	

}

?>