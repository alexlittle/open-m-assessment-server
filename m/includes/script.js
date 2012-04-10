
var DATA_CACHE_EXPIRY = 360; // no of mins before the data should be updated from server;
var SOURCE = '';

$.ajaxSetup({
	url: "../api/?format=json",
	type: "POST",
	headers:{},
	dataType:'json',
	timeout: 20000
});

function showPage(hash){
	if(!loggedIn() && hash != '#register'){
		if(!hash){
			hash = '#home';
		}
		showLogin(hash);
		return;
	}
	dataUpdate();
	$('#content').empty();
	
	if (hash == '#register'){
		showRegister();
	} else if (hash == '#login'){
		showLogin();
	} else if(hash.substring(0,3) == '#qt'){
		loadQuiz(hash.substring(1));
	} else {
		showHome();
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
	if(SOURCE != ""){
		document.location = SOURCE;
	} else {
		showPage('#home');
	}
}

function showHome(){
	$('#content').empty();
	var searchform = $('<div>').attr({'id':'search','class':'formblock'});
	searchform.append($('<div>').attr({'id':'searchtitle','class':'formlabel'}).text("Search quizzes:"));
	var ff = $('<div>').attr({'class':'formfield'});
	var sterms = $('<input>').attr({'id':'searchterms'});
	ff.append(sterms);
	searchform.append(ff);
	$('#content').append(searchform);
	
	var searchresults = $('<div>').attr({'id':'searchresults','class':'formblock'}); 
	$('#content').append(searchresults);
	
	var suggest = $('<div>').attr({'id':'suggest','class':'formblock'});
	suggest.append($('<div>').attr({'id':'suggesttitle','class':'formlabel'}).text("or try one of these:"));
	$('#content').append(suggest);
	var suggestresults = $('<div>').attr({'id':'suggestresults','class':'formblock'}); 
	$('#content').append(suggestresults);
	if(store.get('suggest')){
		var data = store.get('suggest');
		for(var q in data){
		   var quiz = $('<div>').attr({'class':'quizlist clickable','onclick':'document.location="#'+data[q].quizref +'"'});
		   quiz.append(data[q].quiztitle);
		   $('#suggestresults').append(quiz);
	   }
	} else {
		$('#suggestresults').append("Loading suggestions...");
		loadSuggested();
	}
	
	$('#searchterms').keypress(function (event) {
			if (event.keyCode == '13'){
				doSearch();
			}
		});
}

function doSearch(){
	var t = $('#searchterms').val().trim();
	if(t.length > 1){
		$('#searchresults').text('Searching...');
		$.ajax({
			   data:{'method':'search','t':t,'username':store.get('username'),'password':store.get('password')}, 
			   success:function(data){
				   //check for any error messages
				   if(data && !data.error){
					   $('#searchresults').empty();
					   if(data.length == 0){
						   $('#searchresults').append('No results found.');
					   } 
					   for(var q in data){
						   var quiz = $('<div>').attr({'class':'quizlist clickable','onclick':'document.location="#'+data[q].quizref +'"'});
						   quiz.append(data[q].quiztitle);
						   $('#searchresults').append(quiz);
					   }
				   }
			   }
			});
	}
}

function loadQuiz(id){
	document.location = "#"+id;
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
	var quiznavprev = $('<div>').attr({'class':'quiznavprev'}).append($('<input>').attr({'id':'quiznavprevbtn','type':'button','class':'button','value':'<< Prev','onclick':'Q.loadPrevQuestion()'}));
	quiznav.append(quiznavprev);
	var quiznavnext = $('<div>').attr({'class':'quiznavnext'}).append($('<input>').attr({'id':'quiznavnextbtn','type':'button','class':'button','value':'Next >>','onclick':'Q.loadNextQuestion()'}));
	quiznav.append(quiznavnext);
	var clear = $('<div>').attr({'style':'clear:both'});
	$('#content').append(quiznav);
	Q.loadQuestion();
	
	
}

function showLogin(hash){
	$('#content').empty();
	$('#content').append("<h2>Login (or <a href='#register'>Register</a>)</h2>");
	var form =  $('<div>');
	form.append("<div class='formblock'>" +
		"<div class='formlabel' name='lang' id='login_username'>"+getString('login_username')+"</div>" +
		"<div class='formfield'><input type='text' name='username' id='username'></input></div>" +
		"</div>");
	
	form.append("<div class='formblock'>"+
		"<div class='formlabel'name='lang' id='login_password'>"+getString('login_password')+"</div>" +
		"<div class='formfield'><input type='password' name='password' id='password'></input></div>" +
		"</div>");
	
	form.append("<div class='ctrl'><input type='button' name='submit' value='Login' onclick='login(\""+hash+"\")' class='button'></input></div>");
	$('#content').append(form);
}

function showRegister(){
	document.location = '#register';
	$('#content').empty();
	$('#content').append("<h2>Register</h2>");
	var l = $('<div>').attr({'id':'loading'}).html("Registering...");
	$('#content').append(l);
	l.hide();
	var form =  $('<form>').attr({'id':'register'});
	form.append("<div class='formblock'>" +
		"<div class='formlabel'>Email address:</div>" +
		"<div class='formfield'><input type='text' name='email' id='email'></input></div>" +
		"</div>");
	form.append("<div class='formblock'>" +
			"<div class='formlabel'>Password:</div>" +
			"<div class='formfield'><input type='password' name='password' id='password'></input></div>" +
			"</div>");
	form.append("<div class='formblock'>" +
			"<div class='formlabel'>Password (confirm):</div>" +
			"<div class='formfield'><input type='password' name='password_confirm' id='password_confirm'></input></div>" +
			"</div>");
	form.append("<div class='formblock'>" +
			"<div class='formlabel'>First name:</div>" +
			"<div class='formfield'><input type='text' name='firstname' id='firstname'></input></div>" +
			"</div>");
	form.append("<div class='formblock'>" +
			"<div class='formlabel'>Surname:</div>" +
			"<div class='formfield'><input type='text' name='surname' id='surname'></input></div>" +
			"</div>");
	form.append("<div class='ctrl'><input type='button' name='submit' value='Register' onclick='register()' class='button'></input></div>");
	$('#content').append(form);
	//data validation
	$('#register').validate({
		rules: {
			email: {
				required: true,
				email:true
			},
			password: {
				required: true,
				minlength: 6
			},
			password_confirm: {
				required: true,
				minlength: 6
			},
			firstname: {
				required: true
			},
			surname: {
				required: true
			}
		}
		
	});
}

function showLoading(msg){
	var l = $('<div>').attr({'id':'loading'}).html("Loading "+msg+"...");
	$('#content').append(l);
}

function loggedIn(){
	if(store.get('username') == null){
		showLogin();
		return false;
	} 
	return true;
}

function login(hash){
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
				   store.set('displayname',data.name);
				   store.set('password',data.hash);
				   store.set('lastlogin',Date());
				   showUsername();
				   showPage(hash);
			   } else {
				   alert('Login failed');
			   }
		   }, 
		   error:function(data){
			   alert("No connection available. You need to be online to log in.");
		   }
		});
	return false;
}

function register(){
	
	var username = $('#email').val();
	var password = $('#password').val();
	var passwordAgain = $('#password_confirm').val();
	var firstname = $('#firstname').val();
	var lastname = $('#surname').val();
	
	//check passwords match
	if(password != passwordAgain){
		alert('Please check the passwords match');
		return;
	}
	
	if(!$('#register').valid()){
		alert('Please check you have fully completed the form');
		return;
	}
	
	$('#register').hide();
	
	$.ajax({
		   data:{'method':'register','username':username,'password':password,'passwordagain':passwordAgain,'firstname':firstname,'lastname':lastname}, 
		   success:function(data){
			   //check for any error messages
			   if(data.login){
				// save username and password
				   store.set('username',$('#email').val());
				   store.set('displayname',data.name);
				   store.set('password',data.hash);
				   store.set('lastlogin',Date());
				   showUsername();
				   showPage('#home');
			   } else if(data.error) {
				   $('#loading').hide();
				   $('#register').show();
				   alert(data.error);
			   } else {
				   alert('An error occurred, please try again.');
				   $('#loading').hide();
				   $('#register').show();
			   }
		   }, 
		   error:function(data){
			   alert("No connection available. You need to be online to register.");
			   $('#loading').hide();
			   $('#register').show();
		   }
		});
	
	
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
			inQuiz = false;
			store.clear();
			store.init();
			showLogin();
			showUsername();
		}
	}
	
}

function showUsername(){
	$('#logininfo').empty();
	if(store.get('displayname') != null){
		$('#logininfo').text(store.get('displayname') + " ");
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
					   store.set('lastupdate',Date());
				   }
			   }, 
			   error:function(data){
				   // do nothing - will send on next update
			   }
			});
	}
	
	// update suggestions
	loadSuggested();
}

function loadSuggested(){
	$.ajax({
		   data:{'method':'suggest','username':store.get('username'),'password':store.get('password')}, 
		   success:function(data){
			   if(data && !data.error){
				   store.clearKey('suggest');
				   for(var q in data){
					   store.addArrayItem('suggest',data[q]);
				   }
				   store.set('lastupdate',Date());
				   if($('#suggestresults')){
					   $('#suggestresults').empty();
					   var data = store.get('suggest');
						for(var q in data){
						   var quiz = $('<div>').attr({'class':'quizlist clickable','onclick':'document.location="#'+data[q].quizref +'"'});
						   quiz.append(data[q].quiztitle);
						   $('#suggestresults').append(quiz);
					   }
				   }
			   }
		   }, 
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
