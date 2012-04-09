var Q = null;

var inQuiz = false;

function Quiz(){
	
	this.quiz = null;
	this.currentQuestion = 0;
	this.responses = [];
	
	this.init = function(q){
		this.quiz = q;
		console.log(q);
		inQuiz = true;
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
		} else if (q.type == 'shortanswer'){
			this.loadShortAnswer();
		} else if (q.type == 'matching'){
			this.loadMatching(q.r);
		} else {
			console.log("question type not implemented:"+q.type);
		}
	}
	
	this.loadMultichoice = function(resp){
		$('#response').empty();
		for(var r in resp){
			var d = $('<div>').attr({'class':'response'});
			var l = $('<label>').attr({'for':resp[r].refid});
			var o = $('<input>').attr({'type':'radio','value':resp[r].refid,'name':'response','id':resp[r].refid,});
			// find if this question has already been responded to and set response
			if(this.responses[this.currentQuestion] && this.responses[this.currentQuestion].qrtext == resp[r].text){
				o.attr({'checked':'checked'});
			}
			l.append(o);
			l.append(resp[r].text);
			d.append(l);
			
			$('#response').append(d);
		}
	}
	
	this.loadShortAnswer = function(){
		$('#response').empty();
		var o = $('<input>').attr({'type':'text','name':'response','id':'shortanswerresponse','class':'formfield'});
		if(this.responses[this.currentQuestion]){
			o.attr({'value':this.responses[this.currentQuestion].qrtext});
		}
		$('#response').append(o);
	}
	
	this.loadMatching = function(resp){
		$('#response').empty();
		
		for(var r in resp){
			
		}
	}
	
	
	this.saveResponse = function(nav){
		var q = this.quiz.q[this.currentQuestion];
		if(q.type == 'multichoice'){
			return this.saveMultichoice(nav);
		} else if(q.type == 'shortanswer'){
			return this.saveShortAnswer(nav);
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
			o.qrtext = "";
			var feedback = null;
			// mark question and get text
			for(var r in q.r){
				if(q.r[r].refid == opt){
					o.score = q.r[r].score;
					o.qrtext = q.r[r].text;
					if (q.r[r].props.feedback && q.r[r].props.feedback != ''){
						feedback = q.r[r].props.feedback;
					}
				}
			}
			o.score = Math.min(o.score,parseInt(q.props.maxscore));
			this.responses[this.currentQuestion] = o;
			
			// show feedback (if any)
			if(feedback){
				alert("Feedback: "+feedback);
			}
			return true;
		} else {
			if(nav == 'next'){
				return false;
			} else {
				return true;
			}	
		}
	}
	
	this.saveShortAnswer = function(nav){
		var ans = $('#shortanswerresponse').val().trim();
		if(ans != ''){
			var o = Object();
			var q = this.quiz.q[this.currentQuestion];
			o.qid = q.refid;
			o.score = 0;
			o.qrtext = ans;
			var feedback = null;
			// mark question and get text
			for(var r in q.r){
				if(q.r[r].text == ans){
					o.score = q.r[r].score;
					if (q.r[r].props.feedback && q.r[r].props.feedback != ''){
						feedback = q.r[r].props.feedback;
					}
				}
			}
			o.score = Math.min(o.score,parseInt(q.props.maxscore));
			this.responses[this.currentQuestion] = o;
			
			// show feedback (if any)
			if(feedback){
				alert("Feedback: "+feedback);
			}
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
		if(!this.saveResponse('next')){
			alert("You must answer this question before getting your results.");
			return;
		} 
		inQuiz = false;
		$('#content').empty();
		$('#content').append("<h2 name='lang' id='page_title_results'>Your results for '"+ this.quiz.title +"' quiz:</h2>");
		// calculate score
		var total = 0;
		for(var r in this.responses){
			total += this.responses[r].score;
		}
		total = Math.min(total,this.quiz.maxscore);
		var percent = total*100/this.quiz.maxscore;
		$('#content').append("<div id='quiz_results'>"+ percent.toFixed(0) +"%</div>");
		
		var takeQuizBtn = $('<div>').attr({'class': 'button'}).append($("<input>").attr({'type':'button','name':'takeQuiz','value':'Take another Quiz','onclick':'showSelectQuiz()'}));
		$('#content').append(takeQuizBtn);
		
		//save for submission to server
		var content = Object();
		content.quizid = this.quiz.refid;
		content.username = store.get('username');
		content.maxscore = this.quiz.maxscore;
		content.userscore = total;
		content.quizdate = Date.now();
		content.responses = this.responses;
	
		$.ajax({
		   data:{'method':'submit','username':store.get('username'),'password':store.get('password'),'content':JSON.stringify(content)}, 
		   success:function(data){
			   //check for any error messages
			   if(!data || data.error){
				   store.addArrayItem('unsentresults',content);
			   }
		   }, 
		   error:function(data){
			   store.addArrayItem('unsentresults',content);
		   }
		});
	}
	
	this.setNav = function(){
		if(this.currentQuestion == 0){
			$('#quiznavprevbtn').attr('disabled', 'disabled');
		} else {
			$('#quiznavprevbtn').removeAttr('disabled');
		}
		if(this.currentQuestion+1 == this.quiz.q.length){
			$('#quiznavnextbtn').attr({'onclick':'Q.showResults()','value':'Get results'});
		} else {
			$('#quiznavnextbtn').attr({'onclick':'Q.loadNextQuestion()','value':'Next >>'});
		}
	}
	
}