/* 
To download jQuery, go to this documentation:
https://jquery.com/download/

To create jquery.min.js, you can copy contents from CDN then save it as your own jquery.min.js file.
*/

// Convert these characters to HTML escaping characters.
function cleanString(value) {
	return value.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&apos;")
		.replace("\n", "<br>")
}

function displayForums() {
	var url = "http://localhost/pythomyapi/forums.php"
	$.getJSON(url, function(data) {
		/*
			$("#forums")

			is shorthand equivalent for:

			document.getElementById("forums")
		*/

		// Clear list of forums on call of function since this is mostly used for reloading data.
		$("#forums").empty(); 

		// `data` automatically returns responseJSON.
		if (data != null && data.payload != null) {
			if (data.payload.length > 0) {
				var forumsList = data.payload
				for (var index = 0; index < forumsList.length; index++) {
					let forumData = forumsList[index]
					let forumItem = "<li class='forumItem'>" + 
					"<h4><strong>" + cleanString(forumData.title) +"</strong></h4>" +
					"<p><em>Created by: " + forumData.creator.name + " (" +
					forumData.creator.emailAddress + ")</em></p>" + 
					"<p><em>" + forumData.dateAndTime + "</em></p>" + 
					"<p>" + cleanString(forumData.body) +"</p>" +
					"<button onclick='show(" + JSON.stringify(forumData) +
					")'>Show</button>" +
					"<button onclick='showEdit(" + cleanString(JSON.stringify(forumData)) +
					")'>Edit</button>" +
					"<button onclick='deleteForum(" + forumData.forumID +
					")'>Delete</button>" +
					"<div class='divider'></div>" +
					"</li>"
					/*
						<strong> == <b>
						<em> == <i>
					*/
					$("#forums").append(forumItem)
				}
			}
		}
	}).fail(function(error){
		if (error.responseJSON != null) {
			var errorJSON = error.responseJSON // Need to call responseJSON property of `error`.
			var statusCode = parseInt(errorJSON.statusCode)
			if (statusCode == 400) {
				window.alert("You have sent a bad request.")
			}
		}
	})
}

function postForum() {
	var url = "http://localhost/pythomyapi/addforum.php"
	var title = $("#fld_title").val().trim()
	var body = $("#fld_body").val().trim()

	var data = JSON.stringify({
		title: title,
		body: body
	}) // Convert to JSON string.

	$.post(url, data, function(info) {
		if (info != null && info.statusCode != null) {
			var statusCode = parseInt(info.statusCode)
			if (info.statusCode == 201) {
				window.alert("Forum created!")
			} else {
				window.alert("Failed to post forum! Please try again.")
			}
			displayForums() // Update list of forums.
		}
	}).fail(function(error) {
		if (error.responseJSON != null) {
			var errorJSON = error.responseJSON
			if (errorJSON.statusCode != null && errorJSON.statusCode == 403) {
				window.alert("Failed to post forum! Please try again.")
				displayForums() // Update list of forums.
			}
		}
	})
}

// Pass the JSON object of the forum for this to be pre-loaded on show of the modal.
function show(forum) {
	var forumData = forum
	var forumID = forumData.forumID

	// Setting up data to display
	$("#title").html(cleanString(forumData.title))
	$("#body").html(cleanString(forumData.body))
	var creator = forumData.creator.name + " (" + 
		forumData.creator.emailAddress + ")" 
	$("#creator").html(creator)
	$("#timestamp").html(forumData.dateAndTime)
	getComments(forumID)

	// Show
	$("#forum").modal("open")
}

function getComments(forumID) {
	var commentsList = []
	var url = "http://localhost/pythomyapi/comments.php?id=" + forumID
	var numberOfComments = 0
	$.getJSON(url, function(data) {
		if (data.payload != null) {
			$("#forumComments").empty()
			if (data.payload.length > 0) {
				for (var index = 0; index < data.payload.length; index++) {
					var comment = data.payload[index];
					var commentItem = "<li class='forumItem'>" +
					"<p><strong>" + comment.creator.name + " (" +
					comment.creator.emailAddress + ")</strong></p>" +
					"<p><em>" + comment.dateAndTime + "</em></p>" +
					"<p>" + comment.comment + "</p>" +
					"<div class='divider'></div>" +
					"</li>"
					$("#forumComments").append(commentItem)
				}
				numberOfComments = data.payload.length
			}
		}
		$("#numberOfComments").html(numberOfComments)
	}).fail(function(error) {
		if (error.responseJSON != null) {
			var errorJSON = error.responseJSON
			var statusCode = errorJSON.statusCode
			if (statusCode == 400) {
				window.alert(errorJSON.message)
			}
		}
	})
}

function deleteForum(forumID) {
	var url = "http://localhost/pythomyapi/deleteforum.php"
	var data = JSON.stringify({forumID:forumID})
	$.post(url, data, function(data) {
		var statusCode = data.statusCode
		if (statusCode == 202) {
			window.alert(data.message)
			displayForums()
		} else {
			window.alert("Deletion having problem! Please try again.")
		}
	}).fail(function(error) {
		if (error.responseJSON != null) {
			var errorJSON = error.responseJSON
			var statusCode = errorJSON.statusCode
			if (statusCode == 400) {
				window.alert("Deletion having problem! Please try again.")
				displayForums()
			}
		}
	})
}

function showEdit(forum) {
	var forumData = forum
	$("#titleToEdit").val(forumData.title)
	// document.getElementById("titleToEdit").value = forumData.title
	$("#bodyToEdit").val(forumData.body)
	$("#forumID").val(forumData.forumID)
	$("#forumEdit").modal("open")
}

function updateForum() {
	var url = "http://localhost/pythomyapi/updateforum.php"
	var forumID = $("#forumID").val()
	var title = $("#titleToEdit").val()
	var body = $("#bodyToEdit").val()
	var data = JSON.stringify({
		id: forumID,
		title: title,
		body: body
	})
	$.post(url, data, function(data){
		var statusCode = data.statusCode
		if (statusCode == 202) {
			window.alert(data.message)
			displayForums()
		} else {
			window.alert("Editing having problem! Please try again.")
		}
	}).fail(function(error) {
		if (error.responseJSON != null) {
			var errorJSON = error.responseJSON
			var statusCode = errorJSON.statusCode
			if (statusCode == 400) {
				window.alert(errorJSON.message)
				displayForums()
			}
		}
	})
}

$(".modal").modal() // This is required to enable behaviors for Materialize modals. 
displayForums() // Display forums on load of page.