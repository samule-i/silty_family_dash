function deletePost(params){
	var post = document.getElementById("post_" +params.id);
	restore = post.innerHTML
	post.innerHTML = 'Confirm Delete?<br>';
	var confirm = document.createElement("a");
	confirm.setAttribute("href", "#");
	confirm.setAttribute("onclick", "confirmDelete({table:'"+params.table+"', id:'"+params.id+"'});")
	confirm.innerHTML = "Delete";
	post.appendChild(confirm);
	var decline = document.createElement("a");
	decline.setAttribute("href", params.table+".php");
	decline.innerHTML = "Cancel";
	var br = document.createElement("br");
	post.appendChild(br);
	post.appendChild(decline);
}

function confirmDelete(params) {
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "lib/post.php");

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

	var hiddenField = document.createElement("input");
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("name", "action");
	hiddenField.setAttribute("value", "delete");
	form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
}

function newform(params, hiddenfields){
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "lib/post.php");

		for(var key in params){
			var newLabel = document.createElement("label")
			newLabel.setAttribute("for", key);
			newLabel.innerHTML = key;
			form.appendChild(newLabel);
			var textfield = document.createElement("textarea");
			textfield.setAttribute("type", "textarea");
			textfield.setAttribute("name", key);
			form.appendChild(textfield);
		}

	    for(var key in hiddenfields) {
            if(hiddenfields.hasOwnProperty(key)) {
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", key);
				hiddenField.setAttribute("value", hiddenfields[key]);
				form.appendChild(hiddenField);
			}
		}

		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "action");
		hiddenField.setAttribute("value", "create");
		form.appendChild(hiddenField);

		var submit = document.createElement("input");
		submit.setAttribute("type", "submit");
		submit.setAttribute("value", "submit");
		form.appendChild(submit);

		var attatch = document.getElementById("createPost")
        attatch.innerHTML = '';
		attatch.appendChild(form);
}

function editpost(fields, hiddenfields, id){
		var form = document.createElement("form");
		form.setAttribute("method", "post");
		form.setAttribute("action", "lib/post.php");

		for(var key in fields){
			var newLabel = document.createElement("label")
			newLabel.setAttribute("for", key);
			newLabel.innerHTML = key;
			form.appendChild(newLabel);
			var textfield = document.createElement("textarea");
			textfield.setAttribute("type", "textarea");
			textfield.setAttribute("name", key);
			edit = document.getElementById(key+"_"+id).innerHTML;
			textfield.innerHTML = edit;
			form.appendChild(textfield);
		}

	    for(var key in hiddenfields) {
            if(hiddenfields.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", hiddenfields[key]);
            form.appendChild(hiddenField);
         }
		 var hiddenField = document.createElement("input");
		 hiddenField.setAttribute("type", "hidden");
		 hiddenField.setAttribute("name", "id");
		 hiddenField.setAttribute("value", id);
		 form.appendChild(hiddenField);

		 var hiddenField = document.createElement("input");
		 hiddenField.setAttribute("type", "hidden");
		 hiddenField.setAttribute("name", "action");
		 hiddenField.setAttribute("value", "edit");
		 form.appendChild(hiddenField);

		}

		var submit = document.createElement("input");
		submit.setAttribute("type", "submit");
		submit.setAttribute("value", "submit");
		form.appendChild(submit);

		var post = document.getElementById("post_"+id)
		post.innerHTML = ' ';
		post.appendChild(form);
}

function newStar(){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "stars.php");

    var notelabel = document.createElement("label");
    notelabel.setAttribute("for", "note");
    notelabel.innerHTML = "note";
    form.appendChild(notelabel);

    var textarea = document.createElement("textarea");
    textarea.setAttribute("type", "textarea");
    textarea.setAttribute("name", "note");
    form.appendChild(textarea);

    var ownerLabel = document.createElement("label");
    ownerLabel.setAttribute("for", "owner");
    ownerLabel.innerHTML = "owner";
    form.appendChild(ownerLabel);

    var list = document.createElement("input");
    list.setAttribute("list", "users");
    list.setAttribute("name", "users");
    form.appendChild(list);

    var userList= document.createElement("datalist");
    userList.setAttribute("id", "users");
    form.appendChild(userList);

    var i;
    for (i = 0; i < arguments.length; i++){
        var user= document.createElement("option");
        user.setAttribute("value", arguments[i]);
        userList.appendChild(user);
    }

    var submit = document.createElement("input");
    submit.setAttribute("type", "submit");
    submit.setAttribute("value", "submit");
    form.appendChild(submit)

    var parent = document.getElementById("newstar");
    parent.innerHTML = '';
    parent.appendChild(form);
}
