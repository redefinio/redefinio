document.addEventListener("DOMContentLoaded", (e) => {
  loadTemplate();
});

let loadTemplate = () => {
	$.ajax({
  	url: window.templateUrl,
    success: (data) => {
    	let domParser = new DOMParser();
			let template = domParser.parseFromString(data, "text/html");
			let templateHtml = template.getElementById('main-wrap');
			let templateStyles = template.getElementsByTagName('link');

			//Add template HTML
      $('#template').prepend(templateHtml);

      //Add template styles
      $('head').append(templateStyles);

      if(window.isEditing) {
      	prepareToEditTemplate();
      }

      //Add timeout to remove twitches
      setTimeout(() => {
      	$('#loader').removeClass('active');
      }, 1000);
    },

    complete: () => {},

    error: () => {}
  });
}

let prepareToEditTemplate = () => {
  let statusBar = new StatusBar(document.querySelector('#status-bar'));
  window.statusBar = statusBar;

  let zones = document.querySelectorAll('[data-zone]');
  let addBlocks = [];
  for (let i = 0; i < zones.length; i++) {
    addBlocks.push(new AddBlock(zones[i]));
  }

	let blocks = document.querySelectorAll('[data-block-id]');
	let EditableBlocks = [];

	for (let i = 0; i < blocks.length; i++) {
		EditableBlocks.push(new EditableBlock(blocks[i]));
	}

  $('[data-zone]').sortable({
    connectWith: '[data-zone]',
    items: '.item',
    handle: '.move',
    cancel: '',
    helper: 'clone',
    appendTo: 'body',
    zIndex: 10000,
    delay: 100,
    placeholder: 'placeholder',
    activate: (e, ui) => {
      // console.log(e.type, e.target)
      // console.log($(e.target).data('zoneBlockTypes'))
    },
    beforeStop: (e, ui) => {
      // console.log(e.type, e.target)
    },
    change: (e, ui) => {
      // console.log(e.type, e.target)
    },
    create: (e, ui) => {
      // console.log(e.type, e.target)
    },
    deactivate: (e, ui) => {
      // console.log(e.type, e.target)
    },
    out: (e, ui) => {
      // console.log(e.type, e.target)
    },
    over: (e, ui) => {
      // console.log(e.type, e.target)
    },
    receive: (e, ui) => {
      // console.log(e.type, e.target, ui);

      let type = $(ui.item).data('blockType');
      let types = $(e.target).data('zoneBlockTypes').map((obj) => obj.type);

      if (types.indexOf(type) === -1) {
        $(ui.sender).sortable('cancel');
      }

      $('[data-zone]').sortable('enable');
    },
    remove: (e, ui) => {
      // console.log(e.type, e.target)
    },
    sort: (e, ui) => {
      // console.log(e.type, e.target)
    },
    start: (e, ui) => {
      // console.log(e.type, e.target)

      let type = $(ui.item).data('blockType');
      let zones = $('[data-zone]');

      for(let i = 0; i < zones.length; i++) {
        let types = $(zones[i]).data('zoneBlockTypes').map((obj) => obj.type);

        if (types.indexOf(type) === -1) {
          $(zones[i]).sortable('disable');
        }
      }
    },
    stop: (e, ui) => {
      // console.log(e.type, e.target)
    },
    update: (e, ui) => {
      // console.log(e.type, e.target)
    },
  });
}

class EditableBlock {
  constructor(element) {
    this._element = element;
    this._isEditing = false;
    this._hasAddMicroBlock = false;

    this._createControls();
  }

  _createControls() {
    let blockWrapper = document.createElement('div');
    blockWrapper.classList.add('editable-block');

    let blockActionsWrapper = document.createElement('div');
    blockActionsWrapper.classList.add('block-actions');
    blockWrapper.appendChild(blockActionsWrapper);

    let itemPlaceholder = document.createElement('div');
    itemPlaceholder.classList.add('item-placeholder');
    itemPlaceholder.innerHTML = this._element.innerHTML;
    blockWrapper.appendChild(itemPlaceholder);

    if (JSON.parse(this._element.getAttribute('data-is-draggable')) === true) {
      let moveButton = document.createElement('button');
      moveButton.classList.add('move');
      blockActionsWrapper.appendChild(moveButton);
    }

    if (JSON.parse(this._element.getAttribute('data-is-editable')) === true) {
      let editButton = document.createElement('button');
      editButton.classList.add('edit');
      editButton.innerHTML = 'Edit';
      editButton.addEventListener('click', this.edit.bind(this), false);
      blockActionsWrapper.appendChild(editButton);
    }

    if (JSON.parse(this._element.getAttribute('data-is-deletable')) === true) {
      let deleteButton = document.createElement('button');
      deleteButton.classList.add('delete');
      deleteButton.innerHTML = 'Delete';
      deleteButton.addEventListener('click', this.delete.bind(this), false);
      blockActionsWrapper.appendChild(deleteButton);
    }

    if (JSON.parse(this._element.getAttribute('data-is-editable')) === true) {
      let saveButton = document.createElement('button');
      saveButton.classList.add('save');
      saveButton.innerHTML = 'Save';
      saveButton.addEventListener('click', this.save.bind(this), false);
      blockActionsWrapper.appendChild(saveButton);
    }

    while (this._element.firstChild) {
        this._element.removeChild(this._element.firstChild);
    }
    
    this._element.appendChild(blockWrapper);
  }

  _toggleEditing() {
    this._element.querySelector('.editable-block').classList.toggle('is-editing');
    this._isEditing = !this._isEditing;
  }

  edit() {
    let editableElements = this._element.querySelectorAll('[data-key]');
    for (let i = 0; i < editableElements.length; i++) {
      const key = editableElements[i].getAttribute('data-key');
      if(['skill', 'blocks'].indexOf(key) === -1) {
        editableElements[i].setAttribute('contenteditable', true);
      }

      if('blocks' === key && !this._hasAddMicroBlock) {
        this._hasAddMicroBlock = true; 
        new AddMicroBlock(editableElements[i]);
      }
    }

    this._toggleEditing();
  }

  save() {
    let editableElements = this._element.querySelectorAll('[data-key]');
    for (let i = 0; i < editableElements.length; i++) {
      if(['skill', 'blocks'].indexOf(editableElements[i].getAttribute('data-key')) === -1) {
        editableElements[i].setAttribute('contenteditable', false);
      }

      //Data saving
      if(editableElements[i].getAttribute('data-key') !== 'blocks') {
        console.log({[editableElements[i].getAttribute('data-key')]: editableElements[i].innerHTML});  
      }
    }

    this._toggleEditing();
  }

  delete() {
    this._element.parentNode.removeChild(this._element);
    
    window.statusBar.showMessage('You have just deleted block');
  }
};

class StatusBar {
  constructor(element) {
    this._element = element;

    let closeButton = this._element.querySelector('.close');
    closeButton.addEventListener('click', this._hide.bind(this), false);

    this._isActive = false;
    this._animationTimeoutId = null;
  }

  showMessage(message) {
    this._element.classList.remove('is-error');

    let messageEl = this._element.querySelector('.message');
    messageEl.innerHTML = message;
    // this._element.querySelector('.action')

    this._show();
  }

  showError(error) {
    this._element.classList.add('is-error');

    let messageEl = this._element.querySelector('.message');
    messageEl.innerHTML = error;

    this._show();
  }

  _showBar() {
    this._element.classList.add('is-active');
    this._isActive = true;

    clearTimeout(this._animationTimeoutId);
    this._animationTimeoutId = setTimeout(this._hide.bind(this), 5000);
  }

  _show() {
    if(this._isActive) {
      new Promise((resolve, reject) => {
        this._hide();
        setTimeout(resolve, 250);
      }).then(this._showBar.bind(this));
    }
    else {
      this._showBar();
    }
  }

  _hide() {
    this._element.classList.remove('is-active');
    this._isActive = false;
  }
};

class AddBlock {
  constructor(zone) {
    this._element = null;

    this._createAddBlock(zone);
  }

  _createAddBlock(zone) {
    const zoneName = zone.getAttribute('data-zone');
    const zoneTypes = JSON.parse(zone.getAttribute('data-zone-block-types'));

    let blockWrapper = document.createElement('div');
    blockWrapper.classList.add('add-block');

    let button = document.createElement('button');
    button.innerHTML = 'Add block';
    button.addEventListener('click', this._showList.bind(this), false);
    blockWrapper.appendChild(button);

    let zonesList = document.createElement('ul');
    zonesList.classList.add('add-block-items', 'clearfix');
    blockWrapper.appendChild(zonesList)

    for(let type of zoneTypes) {
      let listItem = document.createElement('li');
      listItem.addEventListener('click', () => this._addBlock(zoneName, type), false);

      let listImg = document.createElement('img');
      listImg.classList.add('icon');
      listImg.setAttribute('src', `${window.location.origin}/img/add-block-${type.type}.png`);
      listItem.appendChild(listImg);

      let listName = document.createElement('span');
      listName.classList.add('title');
      listName.innerHTML = type.name;
      listItem.appendChild(listName);

      zonesList.appendChild(listItem);
    }

    zone.appendChild(blockWrapper);

    this._element = blockWrapper;
  }

  _showList() {
    this._element.classList.add('is-active');
  }

  _addBlock(zoneName, type) {
    let templateId = 1;
    this._element.classList.remove('is-active');

    $.ajax({
      url: `${location.protocol}//${location.host}/api/block/${templateId}/${type.type}`,
      success: (data) => {
        let block = decodeURIComponent(JSON.parse(data).data).replace(/\+/g, ' ');
        // console.log(`[data-type="${zoneName}"]`);
        $(`[data-zone="${zoneName}"]`).find('.add-block').before(block);
      },

      complete: () => {
        window.statusBar.showMessage(`You have just added ${type.name} block`)
      },

      error: () => {}
    });

    // console.log(zoneName, type);
  }
};

class AddMicroBlock {
  constructor(microBlockZone) {
    this._element = null;
    this._templateId = 1;
    this._blockId = microBlockZone.dataset.blocksType;
    this._createAddMicroBlock(microBlockZone);
  }

  _createAddMicroBlock(microBlockZone) {
    let blockWrapper = document.createElement('div');
    blockWrapper.classList.add('add-micro-block');

    let button = document.createElement('button');
    button.innerHTML = 'Add Micro block';
    button.addEventListener('click', () => this._addMicroBlock(this, microBlockZone), false);
    blockWrapper.appendChild(button);

    microBlockZone.parentNode.appendChild(blockWrapper);

    this._element = blockWrapper;
  }

  _addMicroBlock(ids, blck) {
    $.ajax({
      url: `${location.protocol}//${location.host}/api/block/${ids._templateId}/${ids._blockId}`,
      success: (data) => {
        let block = decodeURIComponent(JSON.parse(data).data).replace(/\+/g, ' ');
        $(blck).append(block);
      },

      complete: () => {},

      error: () => {}
    });
  }
}