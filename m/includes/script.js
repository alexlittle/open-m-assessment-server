
var DATA_CACHE_EXPIRY = 360; // no of mins before the data should be updated from server;
var LOGIN_EXPIRY = 7; // no days before the user needs to log in again
var PAGE = '';
$.ajaxSetup({
	url: "../api/?format=json",
	type: "POST",
	headers:{},
	dataType:'json',
	timeout: 20000
});

function showPage(hash){
	if(!loggedIn()){
		showLogin();
		return;
	}
	dataUpdate();
	$('#content').empty();
	if(hash == '#home'){
		showHome();
	} else if (hash == '#select'){
		showSelectQuiz();
	} else if (hash == '#download'){
		downloadQuizzesSelect();
	} else if (hash == '#results'){
		
	} else if (hash == '#login'){
		showLogin();
	} else {
		// try to load quiz
		loadQuiz(hash.substring(1));
	}
}

function confirmExitQuiz(page){
	if(inQuiz){
		var endQuiz = confirm("Are you sure you want to leave this quiz?");
		if(endQuiz){
			inQuiz = false;
		} else {
			return;
		}
	}
	showPage('#home');
}
function showHome(){
	document.location = "#home";
	PAGE = '#home';
	var takeQuizBtn = $('<div>').attr({'class': 'button'}).append($("<input>").attr({'type':'button','name':'takeQuiz','value':'Take a Quiz','onclick':'showSelectQuiz()'}));
	$('#content').append(takeQuizBtn);
	var getForOfflineBtn = $('<div>').attr({'class': 'button'}).append($("<input>").attr({'type':'button','name':'getForOffline','value':'Download Quizzes (for use when offline)','onclick':'downloadQuizzesSelect()'}));
	$('#content').append(getForOfflineBtn);
	/*var viewResultsBtn = $('<div>').attr({'class': 'button'}).append($("<input>").attr({'type':'button','name':'viewResults','value':'View Results','onclick':'showResults()'}));
	$('#content').append(viewResultsBtn);*/
}

function showSelectQuiz(){
	document.location = "#select";
	PAGE = '#select';
	$('#content').empty();
	$('#content').append("<h2 name='lang' id='page_title_selectquiz'>"+getString('page_title_selectquiz')+"</h2>");
	var quizzes = store.get('quizlist');
	if(!quizzes){
		showLoading('quiz list');
	}
	for(var q in quizzes){
		var quiz = $('<div>').attr({'class': 'quizlist clickable','onclick':'loadQuiz("'+quizzes[q].id+'")'});
		quiz.html(quizzes[q].name);
		if(store.get(quizzes[q].id)){
			quiz.append(" <small>(Saved for offline use)</small>");
		}
		$('#content').append(quiz);
	}
}

function downloadQuizzesSelect(){
	document.location = "#download";
	PAGE = '#download';
	$('#content').empty();
	$('#content').append("<h2 name='lang' id='page_title_selectquiz'>"+getString('page_title_download')+"</h2>");
	var quizzes = store.get('quizlist');
	if(!quizzes){
		showLoading('quiz list');
	}
	
	for(var q in quizzes){
		if(!store.get(quizzes[q].id)){
			var quiz = $('<div>').attr({'class': 'quizlist'});
			quiz.append($('<input>').attr({'type':'checkbox','id':'download_'+quizzes[q].id,'value':quizzes[q].id,'class':'checkbox'}));
			var l = $('<label>').attr({'for':'download_'+quizzes[q].id,'class':'clickable'});
			l.html(quizzes[q].name);
			quiz.append(l);
			$('#content').append(quiz);
		}
	}
	var downloadBtn = $('<div>').attr({'class': 'button'}).append($("<input>").attr({'type':'button','name':'downloadBtn','value':'Download selected','onclick':'downloadQuizzes()'}));
	$('#content').append(downloadBtn);
}

function downloadQuizzes(){
	$('input[name=^download_]:checked').each(function() {
		var id = $(this).val();
		$.ajax({
			   data:{'method':'getquiz','username':store.get('username'),'password':store.get('password'),'ref':id}, 
			   success:function(data){
				   if(data.error){
					   alert(data.error);
					   return;
				   }
				   //check for any error messages
				   if(data && !data.error){
					   //save to local cache
					   store.set(id, data);
				   }
			   }, 
			   error:function(data){
				   alert("No connection available. You need to be online to download these quizzes.");
			   }
			});
	});
	
}

function loadQuiz(id){
	document.location = "#"+id;
	PAGE = 'quiz';
	$('#content').empty();
	showLoading('quiz');
	// find if this quiz is already in the cache
	var quiz = store.get(id);
	if(!quiz){
		// load from server
		$.ajax({
			   data:{'method':'getquiz','username':store.get('username'),'password':store.get('password'),'ref':id}, 
			   success:function(data){
				   if(data.error){
					   alert(data.error);
					   inQuiz = false;
					   document.location = "#select";
					   return;
				   }
				   //check for any error messages
				   if(data && !data.error){
					   //save to local cache and then load
					   store.set(id, data);
					   showQuiz(id);
				   }
			   }, 
			   error:function(data){
				   alert("No connection available. You need to be online to load this quiz.");
			   }
			});
	} else {
		showQuiz(id);
	}
}

function showQuiz(id){
	$('#content').empty();
	Q = new Quiz();
	Q.init(store.get(id));
	
	var qhead = $('<div>').attr({'id':'quizheader'});
	$('#content').append(qhead);
	
	var question = $('<div>').attr({'id':'question'});
	$('#content').append(question);
	
	var response = $('<div>').attr({'id':'response'});
	$('#content').append(response);
	
	var quiznav = $('<div>').attr({'id':'quiznav'});
	var quiznavprev = $('<div>').attr({'class':'quiznavprev'}).append($('<input>').attr({'id':'quiznavprevbtn','type':'button','value':'<< Prev','onclick':'Q.loadPrevQuestion()'}));
	quiznav.append(quiznavprev);
	var quiznavnext = $('<div>').attr({'class':'quiznavnext'}).append($('<input>').attr({'id':'quiznavnextbtn','type':'button','value':'Next >>','onclick':'Q.loadNextQuestion()'}));
	quiznav.append(quiznavnext);
	var clear = $('<div>').attr({'style':'clear:both'});
	$('#content').append(quiznav);
	Q.loadQuestion();
	
	
}

function showLogin(){
	document.location = "#login";
	$('#content').empty();
	$('#content').append("<h2 name='lang' id='page_title_login'>"+getString('page_title_login')+"</h2>");
	var form =  $('<div>');
	form.append("<div class='formblock'>" +
		"<div class='formlabel' name='lang' id='login_username'>"+getString('login_username')+"</div>" +
		"<div class='formfield'><input type='text' name='username' id='username'></input></div>" +
		"</div>");
	
	form.append("<div class='formblock'>"+
		"<div class='formlabel'name='lang' id='login_password'>"+getString('login_password')+"</div>" +
		"<div class='formfield'><input type='password' name='password' id='password'></input></div>" +
		"</div>");
	
	form.append("<div class='formblock'>" +
			"<div class='formfield'><input type='button' name='submit' value='Login' onclick='login()'></input></div>" +
			"</div>");
	$('#content').append(form);
}

function showLoading(msg){
	var l = $('<div>').attr({'id':'loading'}).html("Loading "+msg+"...");
	$('#content').append(l);
}
function loggedIn(){
	if(store.get('username') == null){
		return false;
	} 
	// check when last login made
	var now = new Date();
	var lastlogin = new Date(store.get('lastlogin'));
	
	if(lastlogin.addDays(LOGIN_EXPIRY) < now){
		logout(true);
		return false;
	} else {
		return true;
	}
}

function login(){
	var username = $('#username').val();
	var password = $('#password').val();
	if(username == '' || password == ''){
		alert("Please enter your username and password");
		return false;
	}
	
	$.ajax({
		   data:{'method':'login','username':username,'password':password}, 
		   success:function(data){
			   //check for any error messages
			   if(data.login){
				// save username and password
				   store.set('username',$('#username').val());
				   store.set('password',$('#password').val());
				   store.set('lastlogin',Date());
				   showUsername();
				   showPage('#home');
			   } else {
				   alert("Login failed");
			   }
		   }, 
		   error:function(data){
			   alert("No connection available. You need to be online to log in.");
		   }
		});
	return false;
}

function logout(force){
	if(force){
		store.clear();
		store.init();
		showLogin();
		showUsername();
	} else {
		var lo = confirm('Are you sure you want to log out?\n\nYou will need an active connection to log in again.');
		if(lo){
			store.clear();
			store.init();
			showLogin();
			showUsername();
		}
	}
	
}

function showUsername(){
	$('#logininfo').empty();
	if(store.get('username') != null){
		$('#logininfo').text(store.get('username') + " ");
		$('#logininfo').append("<a onclick='logout()' name='lang' id='logout'>"+getString('logout')+"</a>");
	} 
}

function dataUpdate(){
	if(!loggedIn()){
		return;
	}
	// check when last update made, return if too early
	var now = new Date();
	var lastupdate = new Date(store.get('lastupdate'));
	if(lastupdate > now.addMins(-DATA_CACHE_EXPIRY)){
		return;
	} 

	// send any unsubmitted responses
	var unsent = store.get('unsentresults');
	
	if(unsent){
		$.ajax({
			   data:{'method':'submit','username':store.get('username'),'password':store.get('password'),'content':JSON.stringify(unsent)}, 
			   success:function(data){
				   if(data && data.result){
					   // all submitted ok so remove array
					   store.clearKey('unsentresults');
				   }
			   }, 
			   error:function(data){
				   // do nothing - will send on next update
			   }
			});
	}
	
	// Get the quiz list from remote server
	$.ajax({
		   data:{'method':'list','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('quizlist',data);
				   store.set('lastupdate',Date());
				   setUpdated();
				   if(PAGE == '#select'){
					   showSelectQuiz();
				   } else if (PAGE == '#download'){
					   downloadQuizzesSelect();
				   }
			   }
		   }, 
		   error:function(data){
		   }
		});	
}

function setUpdated(){
	//$('#last_update').text(store.get('lastupdate'));
}

Date.prototype.addMins= function(m){
    this.setTime(this.getTime() + (m*60000));
    return this;
}

Date.prototype.addHours= function(h){
    this.setHours(this.getHours()+h);
    return this;
}

Date.prototype.addDays= function(d){
    this.setDate(this.getDate()+d);
    return this;
}