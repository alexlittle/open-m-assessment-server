<?php 

include_once('extras.php');
include_once('format.php');
include_once('gift_format.php');


class GIFTImporter {
	
	public $quizid;
	public $quizmaxscore;
	
	public function import($questions){
		$counter = 1;
		foreach ($questions as $q){
			$maxscore = 0;
			switch ($q->qtype){
				case 'truefalse':
					$maxscore = $this->importTrueFalse($q,$counter);
					break;
				case 'multichoice':
					$maxscore = $this->importMultichoice($q,$counter);
					break;
				case 'shortanswer':
					//array_push($IMPORT_INFO, "Short answer question type not yet supported ('".$q->questiontext."')");
					break;
				case 'numerical':
					//array_push($IMPORT_INFO, "Numerical question type not yet supported ('".$q->questiontext."')");
					break;
				case 'essay':
					//array_push($IMPORT_INFO, "Essay question type not yet supported ('".$q->questiontext."')");
					$maxscore = $this->importEssay($q,$counter);
					break;
			}
			$counter++;
			$this->quizmaxscore += $maxscore;
		}
	}
	
	private function importTrueFalse($q,$qcount){
		global $API;
		$questionid = $API->addQuestion($q->questiontext);
		$API->addQuestionToQuiz($this->quizid,$questionid,$qcount);
		if($q->correctanswer == true){
			$responseid = $API->addResponse('True',10);
			$API->addResponsetoQuestion($questionid,$responseid,1);
			$responseid = $API->addResponse('False',0);
			$API->addResponsetoQuestion($questionid,$responseid,2);
		} else {
			$responseid = $API->addResponse('True',0);
			$API->addResponsetoQuestion($questionid,$responseid,1);
			$responseid = $API->addResponse('False',10);
			$API->addResponsetoQuestion($questionid,$responseid,2);
		}
		
		$API->setProp('question', $questionid, 'maxscore', 10);
		$API->setProp('question', $questionid, 'type', 'multichoice');
		return 10;
	}
	
	private function importMultichoice($q,$qcount){
		global $API;
		$questionid = $API->addQuestion($q->questiontext);
		$API->addQuestionToQuiz($this->quizid,$questionid,$qcount);
		for($i=0; $i<count($q->answer); $i++){
			if($q->fraction[$i] == true){
				$responseid = $API->addResponse($q->answer[$i]['text'],10);
				$API->addResponsetoQuestion($questionid,$responseid,$i+1);
			} else {
				$responseid = $API->addResponse($q->answer[$i]['text'],0);
				$API->addResponsetoQuestion($questionid,$responseid,$i+1);
			}
		}
		$API->setProp('question', $questionid, 'maxscore', 10);
		$API->setProp('question', $questionid, 'type', 'multichoice');
		return 10;
	}
	
	private function importEssay($q,$qcount){
		global $API;
		$questionid = $API->addQuestion($q->questiontext);
		$API->addQuestionToQuiz($this->quizid,$questionid,$qcount);
		$API->setProp('question', $questionid, 'maxscore', 0);
		$API->setProp('question', $questionid, 'type', 'essay');
		return 0;
	}
	
}

?>