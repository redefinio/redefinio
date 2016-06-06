document.addEventListener("DOMContentLoaded", (e) => {
  loadTemplate();
});

let loadTemplate = () => {
  API.getCv((data) => {
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

    //Add timeout to remove twitches after loading template
    setTimeout(() => {
      $('#loader').removeClass('active');
    }, 1000);
  });
}

let prepareToEditTemplate = () => {
  let statusBarDom = $('#status-bar');
  window.statusBar = new StatusBar(statusBarDom);

  //Setup zones
  let zones = $('[data-zone]');
  for (let i = 0; i < zones.length; i++) {
    new Zone(zones[i]);
  }

  //Setup blocks
  let blocks = $('[data-block-id]');
  for (let i = 0; i < blocks.length; i++) {
    new Block(blocks[i]);
  }
}

class StatusBar {
  constructor(element) {
    this._element = element[0];

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
}

class Zone {
  constructor(zone) {
    this._addBlock = null;
    this._element = zone;

    this._createAddBlock();
    this._enableDragNDrop();
  }

  _createAddBlock() {
    const zoneName = this._element.getAttribute('data-zone');
    const zoneTypes = JSON.parse(this._element.getAttribute('data-zone-block-types'));

    let blockWrapper = document.createElement('div');
    blockWrapper.classList.add('add-block');

    let button = document.createElement('button');
    button.innerHTML = 'Add block';
    button.addEventListener('click', this._showAddBlockList.bind(this), false);
    blockWrapper.appendChild(button);

    let zonesList = document.createElement('ul');
    zonesList.classList.add('add-block-items', 'clearfix');
    blockWrapper.appendChild(zonesList)

    for(let type of zoneTypes) {
      let listItem = document.createElement('li');
      listItem.addEventListener('click', () => this._addNewBlock(zoneName, type), false);

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

    this._element.appendChild(blockWrapper);
    this._addBlock = blockWrapper;
  }

  _showAddBlockList() {
    this._addBlock.classList.add('is-active');
  }

  _addNewBlock(zoneName, type) {
    this._addBlock.classList.remove('is-active');

    API.getBlock(type.type, (block) => {
      $(`[data-zone="${zoneName}"]`).find('.add-block').before(block);

      window.statusBar.showMessage(`You have just added ${type.name} block`);
    });
  }

  _enableDragNDrop() {
    $(this._element).sortable({
      connectWith: '[data-zone]',
      items: '.item',
      handle: '.move',
      cancel: '',
      helper: 'clone',
      appendTo: 'body',
      zIndex: 10000,
      delay: 100,
      placeholder: 'placeholder',
      activate: (e, ui) => {},
      beforeStop: (e, ui) => {},
      change: (e, ui) => {},
      create: (e, ui) => {},
      deactivate: (e, ui) => {},
      out: (e, ui) => {},
      over: (e, ui) => {},
      receive: (e, ui) => {
        let type = $(ui.item).data('blockType');
        let types = $(e.target).data('zoneBlockTypes').map((obj) => obj.type);

        if (types.indexOf(type) === -1) {
          $(ui.sender).sortable('cancel');
        }

        $('[data-zone]').sortable('enable');
      },
      remove: (e, ui) => {},
      sort: (e, ui) => {},
      start: (e, ui) => {
        let type = $(ui.item).data('blockType');
        let zones = $('[data-zone]');

        for(let i = 0; i < zones.length; i++) {
          let types = $(zones[i]).data('zoneBlockTypes').map((obj) => obj.type);

          if (types.indexOf(type) === -1) {
            $(zones[i]).sortable('disable');
          }
        }
      },
      stop: (e, ui) => {},
      update: (e, ui) => {}
    });
  }
}

class Block {
  constructor(block) {
    this._addBlock = null;
    this._element = block;
    this._blockType = block.dataset.blockType;
    this._childBlockType = null;
    
    this._createControls();
    this._enableMicroBlockDragNDrop();

    if ($(block).find('[data-key="blocks"]').length != 0) {
      this._childBlockType = $(block).find('[data-key="blocks"]')[0].dataset.childBlockType  
      this._createAddBlock();
    }
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

  _enableMicroBlockDragNDrop() {
    $('[data-key="blocks"]').sortable({
      // connectWith: '',
      items: '> div, > li',
      // handle: '.move',
      // cancel: '',
      // helper: 'clone',
      appendTo: 'body',
      zIndex: 10000,
      delay: 100,
      placeholder: 'placeholder',
      activate: (e, ui) => {},
      beforeStop: (e, ui) => {},
      change: (e, ui) => {},
      create: (e, ui) => {},
      deactivate: (e, ui) => {},
      out: (e, ui) => {},
      over: (e, ui) => {},
      receive: (e, ui) => {},
      remove: (e, ui) => {},
      sort: (e, ui) => {},
      start: (e, ui) => {},
      stop: (e, ui) => {},
      update: (e, ui) => {}
    });
  }

  _createAddBlock() {
    let blockWrapper = document.createElement('div');
    blockWrapper.classList.add('add-micro-block');

    let button = document.createElement('button');
    button.innerHTML = 'Add Micro block';
    button.addEventListener('click', this._addMicroBlock.bind(this), false);
    blockWrapper.appendChild(button);
    $(this._element).find('.editable-block').append(blockWrapper);
  }

  _addMicroBlock() {
    API.getBlock(this._childBlockType, (block) => {
      $(this._element).find('[data-key="blocks"]').append(block);
    });
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

const API = {
  getCv: (cb) => {
    $.ajax({
      url: window.templateUrl,
      success: (data) => {
        cb(data);
      },
      complete: () => {},
      error: () => {}
    });
  },

  getBlock: (type, cb) => {
    $.ajax({
      url: `${location.protocol}//${location.host}/api/block/${window.templateId}/${type}`,
      success: (data) => {
        let block = decodeURIComponent(JSON.parse(data).data).replace(/\+/g, ' ');

        cb(block);
      },
      complete: () => {},
      error: () => {}
    });
  }
}