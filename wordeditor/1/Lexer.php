<?php
require_once 'DocTags/Paragraph.php';
require_once 'Converter.php';
require_once 'WordGenerator.php';
class Lexer{
	private $_peek=' ';//记录下一个字符
	//private $_content;//所有内容
	private $_fileName;
	private $_fileHandle;//文件对象
	private $_end=false;//是否结束
	private $_paragraphs=array();//记录段落
	private $_converter;//将html标签转换成word中的格式
	private $_wordGen;//将paragraphs写入到word中
	private $_paragraphNow=null;//当前正在处理的段落
	private $_processTable=false;//表示当前正在处理表格还是普通文本
	private $_paragraphInTable=false;//表示当前正在处理表格单元格中的段落
	private $_fStyleNow=array();//当前字体格式
	private $_counterSpace=false;//当前是否遇到空格
	private $_divNow;//当前div
	public function __construct($fileName=''){
		$this->_fileName=$fileName;
		$this->_fileHandle=fopen($fileName,'r');
		$this->_converter=new Converter();
		$this->_wordGen=new WordGenerator();
		$this->_end=false;
	}
	public function readCh(){
		$this->_peek=fgetc($this->_fileHandle);
		if(feof($this->_fileHandle)){
			$this->_end=true;
			//fclose($this->_fileHandle);

		}
	}
	public function isEnd(){
		return $this->_end;
	}
	public function closeFile(){
		fclose($this->_fileHandle);
	}
	public function scan(){
		while(!$this->_end){
			if($this->_peek=='<'){
				//继续读取直到一个非字母（空格或者'>'）
				$what=$this->readWord();
				echo($what.'in Lexer on line 44'.'<br />');
				if($what=='p'){
					$this->_paragraphNow=new Paragraph();
					if($this->_peek==' '){
						//说明该段落有格式
						$key=$this->readWord();
						$this->readCh();//指针指向"
						$value=$this->readWord();
						$this->_paragraphNow->addPStyle($key,$value);
						$this->readCh();//指针指向>
					}
					$this->_fStyleNow=array();
					while(true){
						$text=$this->readSameText();
						$text=trim($text);
						if($this->_counterSpace==true){
							echo('encounter space in line 61!!'.'<br />');
							$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
							$this->_counterSpace=false;
							continue;
						}
						//$text = preg_replace('//s*/', '', $text);
						if(!$text==''){
							echo('add text'.$text.'in line 68!!'.'<br />');
							$this->_paragraphNow->addText($text,$this->_fStyleNow);
						}
				
						$this->readCh();
						//echo($this->_peek);
						//echo('<br />');
						if($this->_peek=='/'){
							//p结束
							if($this->readWord()!='p'){
								echo 'error format on line 68!!';
							}else{
								echo('end P in Lexer on line 70!!<br />');
							}
							//$paragraph->printSelf();
							$this->addParagraph($this->_paragraphNow);
							//读取下一段字符
							$this->_peek=' ';
							break;
						}else{
							//不是结束符
							if($this->_peek=='b'){
								//换行符
								//$paragraph->setBreak();
								$this->_paragraphNow->addBreak();
								$this->readABreak();
								continue;
							}else if($this->_peek=='t'){
								$this->readTable();
								$this->_processTable=false;
								continue;
							}else if($this->_peek=='h'){
								echo("encounter a hr in Lexer on line 92!!!<br />");
								$this->readHr();
								$this->_paragraphNow->addSection();
								continue;
							}else if($this->_peek=='i'){
								$this->readImg();
								continue;

							}else{
								$this->readRecursively();
								continue;
							}
							

						}
					}
				}else if($what=='h'){
					//遇到标题，标题里面有不同格式的字体？？？？最好标题能统一格式
					$this->_paragraphNow=new Paragraph();
					$this->_paragraphNow->setTitleLevel($this->_peek);
					$this->readTitle();
					$this->addParagraph($this->_paragraphNow);
				}else if($what=='hr'){
					//分页符可能出现在段落外面也可能出现在段落里面，这个是表示出现在段落外面的分页符
					//echo("encounter a hr in Lexer on line 101!!!<br />");
					$this->_paragraphNow=new Paragraph();
					$this->_paragraphNow->setSection();
					$this->readHr();
					$this->addParagraph($this->_paragraphNow);

				}else if($what=='div'){

				}else if($what=='ol'){

				}
			}else{
				$this->readCh();
			}
		}
		$this->closeFile();
		

	}
	public function readStyle() {
		$style=new Style();
		$this->readUntil(array("\""));
		$first='';
		while(true){
			$attr=$first.$this->readUntil(array(':',' '));
			$temp=$this->readUntilLetter();
			$value=$temp.$this->readUntil(array(';',"\""));
			$style->addStyle($attr,$value);
			if($this->_peek=="\""){
				break;
			}
			if($this->_peek==';'){
				$this->readCh();
				$first=$this->_peek;
				if($first=="\""){
					break;
				}
			}
			
		}
		Return $style;
		
	}
	public function readUntilLetter() {
		while(true){
			$this->readCh();
			if($this->isLetter($this->_peek)){
				break;
			}
		}
		Return $this->_peek;
		
	}
    //从下一个字符开始，当遇到某字符时停止读取，返回读到的字符串
	public function readUntil($ends=array()) {
        $value='';
		while(true){
			$this->readCh();
			if(in_array($this->_peek,$ends)){
				break;
			}else{
                $value=$value.$this->_peek;
            }
		}
        return $value;
		
	}
	public function readImg(){
		$this->readWord();//img
		$imgStyle=array();
		$src='';//路径
		while(true){
			$this->readCh();
			if($this->_peek=='/'){
				break;
			}
			$key=$this->readWord($this->_peek);//=
			echo("key of img:".$key);
			echo("<br />");
			$this->readCh();//"
			if($key=='src'){
				while(true){
					$this->readCh();
					if($this->_peek=="\""){
						break;
					}
					$src=$src.$this->_peek;
				}
				
			}else if($key=='align'){
				$value=$this->readWord();
				$imgStyle[$key]=$value;
				
			}else if($key=='height'||$key=='width'){
				$this->readValue("\"");
			}else{
				$this->readCh();//"
			}
			$this->readCh();
			

		}
		if($imgStyle['align']==''){
			$imgStyle['align']='center';//默认居中对齐
		}
		//$src="images/_earth.JPG";
		$this->_paragraphNow->addImg($src,$imgStyle);
		$this->readCh();//>

	}
	public function readTitle(){
		//读取标题，只读取文本，跳过各种格式
		while(true){
			$this->readCh();
			if($this->_peek=='>'){
				break;
			}
		}
		$titleText='';
		$titleEnd=false;
		while(true){
			if($titleEnd){
				break;
			}
			$this->readCh();
			if($this->_peek=='<'){
				while(true){
					$this->readCh();
					if($this->_peek=='>'){
						break;
					}else if($this->_peek=='h'){
						$titleEnd=true;
						break;
					}
				}
			}else{
				$titleText=$titleText.$this->_peek;
			}
		}
		$this->_paragraphNow->setTitleText($titleText);
		
		
	}
	//读取一个分页符
	public function readHr(){
		while(true){
			if($this->_peek=='>'){
				break;
			}
			$this->readCh();
		}
	}
	//读取一个表格
	public function readTable(){
		echo('start reading table!!'.'<br />');
		$this->_processTable=true;
		$this->readWord($this->_peek);
		$styleTable=array();
		$width='';//宽度
		$height='';//高度
		$cellspacing='';
		if($this->_peek==' '){
			//说明该表格有格式
			while(true){
				$attr=$this->readWord();//
				$this->readCh();//指针指向"
				if($attr=='style'){
					$this->_peek='';

					while(true){
						$key=$this->readWord($this->_peek);
						if($key=='width'){
							$width=$this->readStyleValue();//宽度
						}else if($key=='background'){
							$this->readWord();
							$styleTable['bgColor']=$this->readStyleValue();
						}else if($key=='height'){
							$height=$this->readStyleValue();//高度
							
						}
						$this->readCh();
						if($this->_peek=="\""){
							break;
						}else{
							continue;
						}
					}
					

				}else if($attr=='height'){
					$height=$this->readValue("\"");
				}else if($attr=='width'){
					$width=$this->readValue("\"");
				}else if($attr=='cellspacing'){
					$cellspacing=$this->readValue("\"");
				}else{
					echo('encounter '.$attr.' in table in line 270<br />');
					$styleTable[$this->_converter->convertHtmlToWord($attr)]=$this->readValue("\"");
				}
				$this->readCh();
				if($this->_peek=='>'){
					break;
				}
				
			}

		}
		$this->_paragraphNow->addTable($styleTable);
		$this->readSameText();
		echo($this->readWord().'<br />');//tbody
		
		//读取行
		while(true){
			$this->readSameText();//<
			$this->readCh();
			if($this->_peek=='/'){
				echo('tbody end!!!'.'<br />');
				if(!$this->readWord()=='tbody'){
					echo('error format on line 179');
				}
				break;
			}
			echo($this->readWord().'<br />');//tr
			
			$this->_paragraphNow->tableAddRow();//表格添加一行
			while(true){
				$this->readSameText();//<
				$this->readCh();
				if($this->_peek=='/'){
					//一行结束
					if(!$this->readWord()=='tr'){
						echo('error format on line 185!!!');
					}
					break;
				}
				$styleCell=array();
				echo($this->readWord().' in line 310 <br />');//td
				if($this->_peek==' '){
					//单元格有属性
					while(true){
						$attr=$this->readWord();
						$this->readCh();//"
						if($attr=='style'){
							$this->_peek='';
							while(true){
								$key=$this->readWord($this->_peek);
								if($key=='background'){
									$this->readWord();
									$styleCell['bgColor']=$this->readStyleValue();
								}
								$this->readCh();
								if($this->_peek=="\""){
									break;
								}else{
									continue;
								}
							}
						}else{
							$value=$this->readValue("\"");
							if($value==''||$value=='1'){

							}else{
								$styleCell[$attr]=$value;
							}
							

						}
						$this->readCh();
						if($this->_peek=='>'){
							break;
						}
					}
				}
				$this->_paragraphNow->addCell($styleCell);
				$this->_fStyleNow=array();
				//读取单元格的内容
				while(true){
					$text=$this->readSameText();
					$text=trim($text);
					if($this->_counterSpace==true){
						$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						$this->_counterSpace=false;
						continue;
					}
					
					//$text = preg_replace('//s*/', '', $text);
					if(!$text==''){
						$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
					}
					$this->readCh();
					if($this->_peek=='/'){
							//td结束
						$end=$this->readWord();
						if($end!='td'){
							echo 'error format on line 368!! with word '.$end.' <br />';
						}
						//$paragraph->printSelf();
						//$this->addParagraph($paragraph);
						//读取下一段字符
						$this->_peek=' ';
						break;
					}else{
						//不是结束符
						if($this->_peek=='b'){
							//换行符
							//$paragraph->setBreak();
							$this->_paragraphNow->cellAddBreak();
							$this->readABreak();
							continue;
						}else if($this->_peek=='p'){
							echo('encounter p in cell in line 384!!!<br />');
							$this->readParagraphInCell();
							$this->_paragraphInTable=false;
							continue;
						}else{
							$this->readRecursively();
							continue;
						}
							

					}
				}

				

			}
		}
		$this->readSameText();//<
		$this->readCh();
		if($this->_peek=='/'){
			echo('table end!!!'.'<br />');
			if(!$this->readWord()=='table'){
				echo('error format on line 273');
			}
		}


	}
	public function readParagraphInCell(){
		//$this->_paragraphNow=new Paragraph();
		$this->_paragraphInTable=true;
		$this->_paragraphNow->addParagraphInCell();
		$this->readCh();
		if($this->_peek==' '){
			//说明该段落有格式
			$key=$this->readWord();
			$this->readCh();//指针指向"
			$value=$this->readWord();
			$this->_paragraphNow->addPStyleInCell($key,$value);
			$this->readCh();//指针指向>
		}
		$this->_fStyleNow=array();
		while(true){
			$text=$this->readSameText();
			$text=trim($text);
			if($this->_counterSpace==true){
				$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
				$this->_counterSpace=false;
				continue;
			}
			
			//$text = preg_replace('//s*/', '', $text);
			if(!$text==''){
				$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
			}
				
			$this->readCh();
			//echo($this->_peek);
			//echo('<br />');
			if($this->_peek=='/'){
				//p结束
				if($this->readWord()!='p'){
					echo 'error format on line 296!!';
				}
				//$paragraph->printSelf();
				//$this->addParagraph($this->_paragraphNow);
				//读取下一段字符
				$this->_peek=' ';
				break;
			}else{
				//不是结束符
				if($this->_peek=='b'){
					//换行符
					//$paragraph->setBreak();
					$this->_paragraphNow->addBreakToParaInCell();
					$this->readABreak();
					continue;
				}else{
					$this->readRecursively();
					continue;
				}
							

			}
		}
	}
	public function readABreak(){
		//读取一个换行符
		for($i=1;$i<=5;$i++){
			$this->readCh();
		}
	
	}
	//判断是否为字母
	public function isLetter($char=''){
		return preg_match ("/^[A-Za-z]/",  $char);
	}
	//读取一个英文单词
	public function readWord($word=''){
		//$word='';
		while(true){
			$this->readCh();
			if($this->isLetter($this->_peek)){
				$word=$word.$this->_peek;
			}else{
				break;
			}
		}
		return $word;
	}

	//添加一个段落
	public function addParagraph(){
		array_push($this->_paragraphs,$this->_paragraphNow);
	}

	//读取一连串相同格式的文本
	public function readSameText(){
		$text='';
		while(true){
			$this->readCh();
			if($this->_peek=='<'){
				break;
			}else if($this->_peek=='&'){
				$tempText=$this->readSpace();
				if($tempText=='&nbsp;'){
					//如果遇到空格，置当前空格位为true，跳出循环
					$this->_counterSpace=true;
					break;
				}else{
					$text=$text.$tempText;
				}
			}else{
				$text=$text.$this->_peek;
			}

		}
		return $text;
	}

	//读取一个空格，如果是空格则返回空格，不是则返回原字符串
	public function readSpace(){
		$tempText='&';
		$this->readCh();
		if($this->_peek=='n'){
			$tempText=$tempText.$this->_peek;
			$this->readCh();
			if($this->_peek=='b'){
				$tempText=$tempText.$this->_peek;
				$this->readCh();
				if($this->_peek=='s'){
					$tempText=$tempText.$this->_peek;
					$this->readCh();
					if($this->_peek=='p'){
						$tempText=$tempText.$this->_peek;
						$this->readCh();
						if($this->_peek==';'){
							$tempText=$tempText.$this->_peek;
						}else{
							$tempText=$tempText.$this->_peek;
							return $tempText;
						}
					}else{
						$tempText=$tempText.$this->_peek;
						return $tempText;
					}
				}else{
					$tempText=$tempText.$this->_peek;
					return $tempText;
				}
			}else{
				$tempText=$tempText.$this->_peek;
				return $tempText;
			}
		}else{
			$tempText=$tempText.$this->_peek;
			return $tempText;
		}
		return $tempText;
	}
	//读取以指定字符结尾的值
	public function readValue($end=''){
		$value='';
		while(true){
			$this->readCh();
			if($this->_peek==$end){
				break;
			}else{
				$value=$value.$this->_peek;
			}
		}
		return $value;

	}
	//读取span中的属性值,应该被复用
	public function readStyleValue(){
		$color='';
		while(true){
			$this->readCh();
			if($this->_peek==';'){
				break;
			}else{
				$color=$color.$this->_peek;
			}
		}
		return $color;
	}
	//遇到<时，递归读取
	public function readRecursively(){
		$key=$this->readWord($this->_peek);//因为在之前判断是否结束的时候多读取了一个字符
		if($key=='strong'){
			//加粗
			//不可能出现strong 里嵌套strong的情况
			//array_push($fStyle,'bold'=>true);
			$this->_fStyleNow['bold'] = true;
			
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				
				if(!$text==''){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
					
				}
			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSimpleFormat('strong');
					return;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}
		}else if($key=='span'){
			echo('encounter a span!!! in 637'.'<br />');
			$needDeletedStyle=array();//记录在结束之后需要被删除的格式
			if($this->_peek!='>'){
				//指针指向空格

				$this->readWord();//读取style,指向=
				$this->readCh();//指向"
				$key=$this->readWord();
				while(true){
					if($key==''){
						break;
					}
					if($key=='color'){
						$color=$this->readStyleValue();
						//array_push($fStyle,'color'=>$color);
						$this->_fStyleNow['color']=$color;
						array_push($needDeletedStyle,'color');
						
					}else if($key=='font'){
						$key=$this->readWord();
						if($key=='size'){
							//可能需要将html中的大小（px）转换为word中的大小,需要转换器
							$size=$this->readStyleValue();
							//array_push($fStyle,'size'=>$size);
							$this->_fStyleNow['size']=$this->_converter->convertSize($size);
							array_push($needDeletedStyle,'size');

						}else if($key=='family'){
							//无需转换器
							echo('encounter a font-family'.'<br />');
							$family=$this->readStyleValue();
							//array_push($fStyle,'name'=>$family);
							$this->_fStyleNow['name']=$family;
							array_push($needDeletedStyle,'name');
						}
					}else if($key=='background'){
						//需要转换器
						$key=$this->readWord();
						$color=$this->readStyleValue();
						//array_push($fStyle,'fgColor'=>$color);
						$this->_fStyleNow['fgColor']=$this->_converter->convertToFgColor($color);
						array_push($needDeletedStyle,'fgColor');

					}
					$this->readCh();
					if($this->_peek=="\""){
							break;
					}else{
						$key=$this->readWord($this->_peek);
						continue;
					}
				}
				$this->readCh();//指向>
			}
			
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					echo('encounter space in line 694!!'.'<br />');
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				if(!$text==''){
					//echo('add text'.$text.'in line 707!!'.'<br />');
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
				}

			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSpanFormat($needDeletedStyle);
					return;
				}else if($this->_peek=='h'){
					echo("encounter a hr in Lexer on line 729!!!<br />");
					$this->readHr();
					$this->_paragraphNow->addSection();
					continue;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}

		}else if($key=='em'){
			//倾斜
			//array_push($fStyle,'italic'=>true);
			$this->_fStyleNow['italic']=true;
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				if(!$text==''){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
				}
			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSimpleFormat('em');
					return;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}

		}else if($key=='u'){
			//下划线
			//array_push($fStyle,'underline'=>'single');//先使用简单的单下划线
			$this->_fStyleNow['underline']='single';
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				if(!$text==''){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
				}
			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSimpleFormat('u');
					return;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}
		}else if($key=='sub'){
			//下标
			$this->_fStyleNow['subScript']=true;
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				if(!$text==''){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
				}
			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSimpleFormat('sub');
					return;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}

		}else if($key=='sup'){
			//上标
			$this->_fStyleNow['superScript']=true;
			while(true){
				$text=$this->readSameText();
				$text=trim($text);
				if($this->_counterSpace==true){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text.' ',$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text.' ',$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text.' ',$this->_fStyleNow);
					}
					$this->_counterSpace=false;
					continue;
				}
				if(!$text==''){
					if($this->_processTable){
						if($this->_paragraphInTable){
							$this->_paragraphNow->addTextToParaInCell($text,$this->_fStyleNow);
						}else{
							$this->_paragraphNow->addCellText($text,$this->_fStyleNow);
						}
						
					}else{
						$this->_paragraphNow->addText($text,$this->_fStyleNow);
					}
				}
			
				$this->readCh();
				if($this->_peek=='/'){
					//结束
					$this->endSimpleFormat('sup');
					return;
				}else{
					//不是结束符而是新增格式
					$this->readRecursively();
					continue;

				}
			}
		}


	}
	//结束一个span格式（在数组中删除span格式中带有的styles）
	public function endSpanFormat($needDeletedStyle=array()){
		$key=$this->readWord();//指针指向>
		if(!$key=='span'){
			echo "error format on line 636!!";
			return;
		}else{
			//删除格式,直接自己遍历重组了= =
			$tempFStyle=array();
			foreach($this->_fStyleNow as $key=>$value){
				if(in_array($key,$needDeletedStyle)){
					continue;
				}else{
					//array_push($tempFStyle,$key=>$value);
					$tempFStyle[$key]=$value;
				}
			}
			

		}
		$this->_fStyleNow=$tempFStyle;


	}

	//结束一个普通格式标签
	public function endSimpleFormat($format=''){
		$key=$this->readWord();//指针指向>
		if(!$key==$format){
			echo "error format on line 661!!";
			return;
		}else{
			//删除格式,直接自己遍历重组了= =
			$tempFStyle=array();
			foreach($this->_fStyleNow as $key=>$value){
				if($key==$this->_converter->convertHtmlToWord($format)){
					continue;
				}else{
					//array_push($tempFStyle,$key=>$value);
					$tempFStyle[$key]=$value;
				}
			}

		}
		$this->_fStyleNow=$tempFStyle;


	}
	public function printParagraphs(){
		foreach($this->_paragraphs as $paragraph){
			$paragraph->printSelf();

		}
	}
	public function generateWord(){
		$this->_wordGen->generateParagraphs($this->_paragraphs);
		$this->_wordGen->generate();
	}


}
?>