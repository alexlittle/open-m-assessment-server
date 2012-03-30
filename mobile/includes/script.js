
var PAGE = "";
var DATA_CACHE_EXPIRY = 360; // no of mins before the data should be updated from server;
var LOGIN_EXPIRY = 7; // no days before the user needs to log in again

$.ajaxSetup({
	url: "../api/?format=json",
	type: "POST",
	headers:{},
	dataType:'json',
	timeout: 20000
});

function showPage(page){
	if(!loggedIn()){
		showLogin();
		return;
	}
	dataUpdate();
	PAGE = page;
	$('#content').empty();
	if(page == 'home'){
		showHome();
	} 
}

function showHome(){
	var takeQuizBtn = $('<div>').attr({'class': 'homeBtn'}).append($("<input>").attr({'type':'button','name':'takeQuiz','value':'Take a Quiz','onclick':'showSelectQuiz()'}));
	$('#content').append(takeQuizBtn);
	var viewResultsBtn = $('<div>').attr({'class': 'homeBtn'}).append($("<input>").attr({'type':'button','name':'viewResults','value':'View Results','onclick':'showResults()'}));
	$('#content').append(viewResultsBtn);
}

function showSelectQuiz(){
	$('#content').empty();
	$('#content').append("<h2 name='lang' id='page_title_selectquiz'>"+getString('page_title_selectquiz')+"</h2>");
	var quizzes = store.get('quizlist');
	for(var q in quizzes){
		var quiz = $('<div>').attr({'class': 'quizlist clickable','onclick':'loadQuiz("'+quizzes[q].id+'")'}).html(quizzes[q].name);
		$('#content').append(quiz);
	}
}

function loadQuiz(id){
	// find if this quiz is already in the cache
	var quiz = store.get(id);
	if(!quiz){
		// load from server
		$.ajax({
			   data:{'method':'getquiz','username':store.get('username'),'password':store.get('password'),'ref':id}, 
			   success:function(data){
				   if(data.error){
					   alert(data.error);
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
				   showPage('home');
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

	// Get the quiz list from remote server
	$.ajax({
		   data:{'method':'list','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   //check for any error messages
			   if(data && !data.error){
				   store.set('quizlist',data);
				   store.set('lastupdate',Date());
				   setUpdated();
			   }
		   }, 
		   error:function(data){
		   }
		});
	
	// TODO send any unsubmitted responses
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