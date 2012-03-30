var Q = null;

function Quiz(){
	
	this.quiz = null;
	this.currentQuestion = 0;
	this.responses = [];
	
	this.init = function(q){
		this.quiz = q;
		console.log(this.quiz);
	}
	
	this.setHeader = function(){
		$('#quizheader').html(this.quiz.title + " Q" +(this.currentQuestion+1) + " of "+ this.quiz.q.length);
	}
	
	this.loadNextQuestion = function(){
		if(this.saveResponse()){
			this.currentQuestion++;
			this.loadQuestion();
		} else {
			alert("You must answer this question before continuing.");
		}
	}
	
	this.loadPrevQuestion = function(){
		if(this.saveResponse()){
			this.currentQuestion--;
			this.loadQuestion();
		}
	}
	
	this.loadQuestion = function(){
		this.setHeader();
		this.setNav();
		$('#question').html(this.quiz.q[this.currentQuestion].text);
		this.loadResponses(this.quiz.q[this.currentQuestion]);
	}
	
	this.loadResponses = function(q){
		if(q.type == 'multichoice'){
			this.loadMultichoice(q.r);
		} else {
			console.log("question type not implemented:"+q.type);
		}
	}
	
	this.loadMultichoice = function(resp){
		$('#response').empty();
		for(var r in resp){
			var o = $('<input/>').attr({'type':'radio','value':resp[r].refid,'name':'response'});
			$('#response').append(o);
			$('#response').append(resp[r].text);
			$('#response').append("<br/>");
		}
	}
	
	this.saveResponse = function(){
		var q = this.quiz.q[this.currentQuestion];
		if(q.type == 'multichoice'){
			return this.saveMultichoice(q.refid);
		} else {
			console.log("question type not implemented:"+q.type);
		}
	}
	
	this.saveMultichoice = function(qrefid){
		var opt = $('input[name=response]:checked').val();
		if(opt){
			
			return true;
		} else {
			return false;
		}
	}
	
	this.showResults = function(){
		
	}
	
	this.setNav = function(){
		if(this.currentQuestion == 0){
			$('#quiznavprev').attr('disabled', 'disabled');
		} else {
			$('#quiznavprev').removeAttr('disabled');
		}
		if(this.currentQuestion+1 == this.quiz.q.length){
			$('#quiznavnext').attr({'onclick':'Q.showResults()','value':'Get results'});
		} else {
			$('#quiznavnext').attr({'onclick':'Q.loadNextQuestion()','value':'Next >>'});
		}
	}
	
}