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
		.replace(/'/g,"&apos;")
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

displayForums() // Display forums on load of page.