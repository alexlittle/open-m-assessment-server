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
		if(this.saveResponse('next')){
			this.currentQuestion++;
			this.loadQuestion();
		} else {
			alert("You must answer this question before continuing.");
		}
	}
	
	this.loadPrevQuestion = function(){
		this.saveResponse('prev')
		this.currentQuestion--;
		this.loadQuestion();

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
			// find if this question has already been responded to and set response
			if(this.responses[this.currentQuestion]){
				if(this.responses[this.currentQuestion].text == resp[r].text){
					o.attr({'checked':'checked'});
				}
			}
			$('#response').append(o);
			$('#response').append(resp[r].text);
			$('#response').append("<br/>");
		}
	}
	
	this.saveResponse = function(nav){
		var q = this.quiz.q[this.currentQuestion];
		if(q.type == 'multichoice'){
			return this.saveMultichoice(nav);
		} else {
			console.log("question type not implemented:"+q.type);
		}
	}
	
	this.saveMultichoice = function(nav){
		var opt = $('input[name=response]:checked').val();
		if(opt){
			var o = Object();
			var q = this.quiz.q[this.currentQuestion];
			o.qid = q.refid;
			o.score = 0;
			o.text = "";
			// mark question and get text
			for(var r in q.r){
				if(q.r[r].refid == opt){
					o.score = q.r[r].score;
					o.qrtext = q.r[r].text;
				}
			}
			o.score = Math.min(o.score,parseInt(q.props.maxscore));
			this.responses[this.currentQuestion] = o;
			return true;
		} else {
			if(nav == 'next'){
				return false;
			} else {
				return true;
			}	
		}
	}
	
	this.showResults = function(){
		if(!this.saveResponse()){
			alert("You must answer this question before getting your results.");
			return;
		} 
		$('#content').empty();
		$('#content').append("<h2 name='lang' id='page_title_results'>Your results for "+ this.quiz.title +":</h2>");
		// calculate score
		var total = 0;
		for(var r in this.responses){
			total += this.responses[r].score;
		}
		total = Math.min(total,this.quiz.maxscore);
		var percent = total*100/this.quiz.maxscore;
		$('#content').append("<div id='quiz_results'>"+ percent.toFixed(0) +"%</div>");
		
		//save for submission to server
		var content = Object();
		content.quizid = this.quiz.refid;
		content.username = store.get('username');
		content.maxscore = this.quiz.maxscore;
		content.userscore = percent;
		content.quizdate = Date.now();
		content.responses = this.responses;

		$.ajax({
			   data:{'method':'submit','username':store.get('username'),'password':store.get('password'),'content':JSON.stringify(content)}, 
			   success:function(data){
				   //check for any error messages
				   if(!data || !data.error){
					   store.set('result_'+ content.quizdate, content);
					   console.log('error sending');
				   }
			   }, 
			   error:function(data){
				   store.set('result_'+ content.quizdate, content);
			   }
			});
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