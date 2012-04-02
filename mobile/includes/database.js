
var store = new Store();
store.init();

function Store(){
	
	this.init = function(){
		if (!localStorage) {
			localStorage.setItem('username', null);
			localStorage.setItem('password', null);
			localStorage.setItem('lang', 'EN');
			localStorage.setItem('quizlist', null);
			localStorage.setItem('results', null);
		}
	}
	
	this.get = function(key){
		var value = localStorage.getItem(key);
	    return value && JSON.parse(value);
	}
	
	this.set = function(key,value){
		localStorage.setItem(key,JSON.stringify(value));
	}
	
	this.clear = function(){
		localStorage.clear();
	}
	
	this.clearKey = function(key){
		this.set(key,null);
	}
	
	this.addArrayItem = function(key,value){
		//get current array
		var c = this.get(key);
		var count = 0;
		if(!c){
			c = [];
		} else {
			count = c.length;
		}
		c[count] = value;
		this.set(key,c);
	}
	
}