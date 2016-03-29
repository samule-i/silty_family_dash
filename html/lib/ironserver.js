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


function label(name){
    var label = document.createElement("label");
    label.setAttribute("for", name);
    label.innerHTML = name+':';
    return label;
}

function textarea(name){
    var textarea = document.createElement("textarea");
    textarea.setAttribute("type", "textarea");
    textarea.setAttribute("name", name);
    return textarea;
}

function list(){
    var list = document.createElement("input");
    list.setAttribute("list", "users");
    list.setAttribute("name", "owner");
    return list
}

function datalist(){
    var datalist= document.createElement("datalist");
    datalist.setAttribute("id", "users");

    var i;
    for (i = 0; i < arguments.length; i++){
        var user= document.createElement("option");
        user.setAttribute("value", arguments[i]);
        datalist.appendChild(user);
    }
    return datalist;
}

function postid(id){
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "id");
    hiddenField.setAttribute("value", id);
    return hiddenField
}

function action(action){
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "action");
    hiddenField.setAttribute("value", action);
    return hiddenField
}

function submit(){
    var submit = document.createElement("input");
    submit.setAttribute("type", "submit");
    submit.setAttribute("value", "submit");
    return submit;
}

function archive(table, id){
    if(confirm("Are you sure you want to archive this post?")==true){
    var form=document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "lib/post.php");

    form.appendChild(postid(id));
    form.appendChild(action("archive"));

    var hTable = document.createElement("input");
    hTable.setAttribute("type", "hidden");
    hTable.setAttribute("name", "table");
    hTable.setAttribute("value", table);
    form.appendChild(hTable);

    document.body.appendChild(form);
    form.submit();}
}

function newStar(){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "stars.php");

    form.appendChild(label("note"));
    form.appendChild(textarea("note"));
    form.appendChild(label("owner"));
    form.appendChild(list());
    form.appendChild(datalist.apply(this, arguments));

    var submit = document.createElement("input");
    submit.setAttribute("type", "submit");
    submit.setAttribute("value", "submit");
    form.appendChild(submit);

    var parent = document.getElementById("newstar");
    parent.innerHTML = '';
    parent.appendChild(form);
}

function newReward(){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "rewards.php");

    form.appendChild(label("title"));
    form.appendChild(textarea("title"));
    form.appendChild(label("note"));
    form.appendChild(textarea("note"));
    form.appendChild(label("cost"));
    form.appendChild(textarea("cost"));
    form.appendChild(label("image"));
    form.appendChild(textarea("image"));
    form.appendChild(label("link"));
    form.appendChild(textarea("link"));
    form.appendChild(label("owner"));
    form.appendChild(list());
    form.appendChild(datalist.apply(this, arguments));
    form.appendChild(action("new"));
    form.appendChild(submit());

    var parent = document.getElementById("newform");
    parent.innerHTML = '';
    parent.appendChild(form);
}

function editReward(id){
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "rewards.php");

    var title=textarea("title");
    var note=textarea("note");
    var cost=textarea("cost");
    var image=textarea("image");
    var link=textarea("link");

    form.appendChild(label("title"));
    form.appendChild(title);
    form.appendChild(label("note"));
    form.appendChild(note);
    form.appendChild(label("cost"));
    form.appendChild(cost);
    form.appendChild(label("image"));
    form.appendChild(image);
    form.appendChild(label("link"));
    form.appendChild(link);
    form.appendChild(postid(id));
    form.appendChild(action("edit"));
    form.appendChild(submit());

    title.innerHTML = document.getElementById("title_"+id).innerHTML;
    note.innerHTML = document.getElementById("note_"+id).innerHTML;
    cost.innerHTML = document.getElementById("cost_"+id).innerHTML;
    image.innerHTML = document.getElementById("image_"+id).src;
    link.innerHTML = document.getElementById("link_"+id).innerHTML;

    var parent = document.getElementById("post_"+id);
    parent.innerHTML = '';
    parent.appendChild(form);
}

function awardReward(id){
    if(confirm("Are you sure you want to award this post?")==true){
    var form=document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "rewards.php");

    form.appendChild(postid(id));
    form.appendChild(action("award"));

    document.body.appendChild(form);
    form.submit();
    }
}
