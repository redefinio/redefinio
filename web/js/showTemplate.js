document.addEventListener("DOMContentLoaded", function(e) {
  loadTemplate();
});

var loadTemplate = function() {
	$.ajax({
  	url: window.templateUrl,
    success: function(data) {
    	var domParser = new DOMParser();
			var template = domParser.parseFromString(data, "text/html");
			var templateHtml = template.getElementById('main-wrap');
			var templateStyles = template.getElementsByTagName('link');

			//Add template HTML
      $('#template').prepend(templateHtml);

      //Add template styles
      $('head').append(templateStyles);

      if(window.isEditing) {
      	prepareToEditTemplate();
      }

      //Add timeout to remove twitches
      setTimeout(function() {
      	$('#loader').removeClass('active');
      }, 1000);
    },

    complete: function() {

    },

    error: function() {}
  });
}

var prepareToEditTemplate = function() {
	var blocks = document.getElementsByClassName('item');

	for (var i = 0; i < blocks.length; i++) {
		var blockWrapper = document.createElement('div');
		blockWrapper.classList.add('editable-block');

		var blockActionsWrapper = document.createElement('div');
		blockActionsWrapper.classList.add('block-actions');
		blockWrapper.appendChild(blockActionsWrapper);

		var itemPlaceholder = document.createElement('div');
		itemPlaceholder.classList.add('item-placeholder');
		itemPlaceholder.innerHTML = blocks[i].innerHTML;
		blockWrapper.appendChild(itemPlaceholder);

		var moveButton = document.createElement('button');
		moveButton.classList.add('move');
		blockActionsWrapper.appendChild(moveButton);

		var editButton = document.createElement('button');
		editButton.classList.add('edit');
		editButton.innerHTML = 'Edit';
		blockActionsWrapper.appendChild(editButton);

		var deleteButton = document.createElement('button');
		deleteButton.classList.add('delete');
		deleteButton.innerHTML = 'Delete';
		blockActionsWrapper.appendChild(deleteButton);

		var sth = document.createElement('div');
		sth.appendChild(blockWrapper)

		blocks[i].innerHTML = sth.innerHTML;
	}
}
