document.addEventListener("DOMContentLoaded", (e) => {
  loadTemplate(window.templateId);
});

$('.edit-url-btn').on('click', () => {
  var copyTextarea = document.querySelector('.edit-url');
  copyTextarea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';

    $('.text-copied').addClass('active');
    setTimeout(function() {
      $('.text-copied').removeClass('active');
    }, 500);
  } catch (err) {}
});
$('.template').on('click', (evebt) => {
  let templateId = $(event.target).parent('.template').data('templateId');
  loadTemplate(templateId);
});

let loadTemplate = (templateId) => {
  activateLoader();
  API.getCv(templateId, (data) => {
    let domParser = new DOMParser();
    let template = domParser.parseFromString(data, "text/html");
    let templateHtml = template.getElementById('main-wrap');
    let templateStyles = template.getElementsByTagName('link');

    //Add template HTML
    $('#template').html(templateHtml);

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
  let zones = $('[data-zone-block-types]');
  for (let i = 0; i < zones.length; i++) {
    new Zone(zones[i]);
  }

  //Setup blocks
  let blocks = $('[data-block-id]');
  for (let i = 0; i < blocks.length; i++) {
    new Block(blocks[i]);
  }
}

let activateLoader = () => {
    let loader = document.createElement('div');
    let loaderIcon = document.createElement('div');

    loader.setAttribute('id', 'loader');
    loader.setAttribute('class', 'active');
    loaderIcon.setAttribute('class', 'signal');

    loader.append(loaderIcon);

    $('#template').append(loader);
}

class StatusBar {
  constructor(element) {
    this._element = element[0];
    
    var _timer;

    let closeButton = this._element.querySelector('.close');
    closeButton.addEventListener('click', this._hide.bind(this), false);
    
    this.undoButton = this._element.querySelector('.action');

    this._isActive = false;
  }

  showMessage(message) {
    this._element.classList.remove('is-error');

    let messageEl = this._element.querySelector('.message');
    messageEl.innerHTML = message;
    this._show();
    
    return new Promise((resolve, reject) => {  
      _timer = setTimeout(() => {        
        this._hide();
        resolve();
      }, 5000);
      
      Rx.Observable.fromEvent(this.undoButton, 'click')
        .subscribe(() => {
          window.clearTimeout(_timer);
          this._hide();
          reject();
      });
    });
  }

  showError(error) {
    this._element.classList.add('is-error');

    let messageEl = this._element.querySelector('.message');
    messageEl.innerHTML = error;

    this._show();
  }

  _show() {
    this._element.classList.add('is-active');
    this._isActive = true;
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
      let newBlock = $(block)[0];

      $(`[data-zone="${zoneName}"]`).find('.add-block').before(newBlock);
      new Block(newBlock);

      window.statusBar.showMessage(`You have just added ${type.name} block`);
    });
  }

  _enableDragNDrop() {
    $(this._element).sortable({
      connectWith: '[data-zone]',
      items: '.item',
      handle: '.move-block',
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

        if ($(zones[i]).hasClass('ui-sortable')) {
          $('[data-zone]').sortable('enable');
        }
      },
      remove: (e, ui) => {},
      sort: (e, ui) => {},
      start: (e, ui) => {
        let type = $(ui.item).data('blockType');
        let zones = $('[data-zone]');

        for(let i = 0; i < zones.length; i++) {
          let types = [];
          if ($(zones[i]).data('zoneBlockTypes') !== undefined) {
            types = $(zones[i]).data('zoneBlockTypes').map((obj) => obj.type);
          }

          if (types.indexOf(type) === -1 && $(zones[i]).hasClass('ui-sortable')) {
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
    this._fixPlaceholders();

    if ($(block).find('[data-key="blocks"]').length != 0) {
      this._childBlockType = $(block).find('[data-key="blocks"]')[0].dataset.childBlockType;

      this._createMicroBlockControls();
      this._enableMicroBlockDragNDrop();
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
      moveButton.classList.add('move', 'move-block');
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

  _fixPlaceholders() {
    let placeholders = $(this._element).find('[data-placeholder]');
    for(let i = 0; i < placeholders.length; i++) {
      let el = $(placeholders[i])[0];
      let placeholder = el.dataset.placeholder;
      if(el.innerHTML.indexOf('{{') > -1) {
        $(el).html(placeholder);
      }
    }
  }

  _createMicroBlockControls() {
    $('[data-key="blocks"] > div, [data-key="blocks"] > li').addClass('editable-micro-block');

    let blockActionsWrapper = document.createElement('div');
    blockActionsWrapper.classList.add('block-actions');

    let moveButton = document.createElement('button');
    moveButton.classList.add('move', 'move-micro-block');
    blockActionsWrapper.appendChild(moveButton);

    let deleteButton = document.createElement('button');
    deleteButton.classList.add('delete');
    deleteButton.innerHTML = 'Delete';
    $(deleteButton).on('click', this.deleteMicroBlock.bind(this));
    blockActionsWrapper.appendChild(deleteButton);

    $('[data-key="blocks"] > div, [data-key="blocks"] > li').remove('.block-actions');
    $('[data-key="blocks"] > div, [data-key="blocks"] > li').append(blockActionsWrapper);
  }

  _enableMicroBlockDragNDrop() {
    $('[data-key="blocks"]').sortable({
      // connectWith: '',
      items: '> div, > li',
      handle: '.move-micro-block',
      cancel: '',
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
      this._createMicroBlockControls();
      this._fixPlaceholders();

      //TODO: refactor edit function
      let editableElements = this._element.querySelectorAll('[data-key]');
      for (let i = 0; i < editableElements.length; i++) {
        const key = editableElements[i].getAttribute('data-key');
        if(['skill', 'blocks'].indexOf(key) === -1) {
          editableElements[i].setAttribute('contenteditable', true);
        }
      }
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
      if(['blocks'].indexOf(key) === -1) {
        editableElements[i].setAttribute('contenteditable', true);
      }
    }

    let sliders = $(this._element).find('.skills');
    for(let i = 0; i < sliders.length; i++) {
      if($(sliders[i]).parent().find('.slider').length === 0) {
        let slider = document.createElement('div');
        slider.classList.add('slider');
        let value = $(sliders[i]).parent('.skills-group').attr('data-value');
        $(slider).slider({
          range: 'max',
          min: 0,
          max: 10,
          value: value,
          slide: function( event, ui ) {
              var value = ui.value;
              $(this).parent(".skills-group").attr("data-value", ui.value);
          }
        });

        $(sliders[i]).after(slider);
      }
    }

    this._toggleEditing();
  }

  save() {
    let editableElements = this._element.querySelectorAll('[data-key]');

    //Data saving
    let counter = 0;
    let data = {};
    data['blockId'] = this._element.dataset.blockId || 0;
    data['blockType'] = this._element.dataset.blockType;
    data['zone'] = $(this._element).parent().data('zone'); 
    data['fields'] = {};


    for (let i = 0; i < editableElements.length; i++) {

      if(editableElements[i].getAttribute('data-key') !== 'blocks') {
        if(data['fields']['blocks'] !== undefined) {  
          const keysCount = $(this._element).find('[data-key="blocks"]').find('[data-key]');
          const sameKeysCount = $(this._element).find('[data-key="blocks"]').find('[data-key="' + keysCount[0].getAttribute('data-key') + '"]')

          let obj = {};
          for(let j = 0; j < (keysCount.length / sameKeysCount.length); j++) {
            let dataValue = (editableElements[i + j].getAttribute('data-value'))? editableElements[i + j].getAttribute('data-value'): editableElements[i + j].innerHTML;
            let dataKey = editableElements[i + j].getAttribute('data-key');
            obj[dataKey] = dataValue;
          }

          i+= keysCount.length / sameKeysCount.length - 1;

          data['fields']['blocks'].push(obj);
        }
        else {
          data['fields'][editableElements[i].getAttribute('data-key')] = editableElements[i].innerHTML;  
        }
      }
      else {
        data['fields']['blocks'] = [];
      }
    }

    for (let z = 0; z < editableElements.length; z++) {
        if (['blocks'].indexOf(editableElements[z].getAttribute('data-key')) === -1) {
            editableElements[z].setAttribute('contenteditable', false);
        }
    }

    API.saveBlock(data, () => {

    });

    this._toggleEditing();
  }

  delete() {
    let blockId = this._element.getAttribute('data-block-id');
    var element = this._element;
    element.classList.add('hidden');
    window.statusBar.showMessage('You have just deleted block').then(function () {
      API.deleteBlock(blockId, () => {
        element.parentNode.removeChild(element);
      });
    }).catch(function (reason) {
      element.classList.remove('hidden');
    });

  }

  deleteMicroBlock(e) {
    $(e.target).parent().parent().detach();
  }
};

const API = {

  getCv: (templateId, cb) => {
    $.ajax({
      url: `${window.templateUrl}${templateId}/template`,
      success: (data) => {
        cb(data);
      },
      complete: () => {},
      error: () => {}
    });
  },

  getBlock: (type, cb) => {
    $.ajax({
      url: `${apiUrl}/block/${window.templateId}/${type}`,
      success: (data) => {
        let block = decodeURIComponent(JSON.parse(data).data).replace(/\+/g, ' ');

        cb(block);
      },
      complete: () => {},
      error: () => {}
    });
  },

  saveBlock: (block, cb) => {
    block.cvId = cvId;

    if (block.blockId !== 0) {
      $.ajax({
        url: `${apiUrl}/block/${window.cvId}/${block.zone}/${block.blockId}`,
        method: 'PUT',
        data: block,
        success: (data) => {
          cb(true);
        },
        complete: () => {},
        error: () => {}
      });
    } else {
      $.ajax({
        url: `${apiUrl}/block/${window.cvId}/${block.zone}`,
        method: 'POST',
        data: block,
        success: (data) => {
          cb(true);
        },
        complete: () => {},
        error: () => {}
      });
    }
  },
  
  deleteBlock: (blockId, cb) => {
    if (blockId !== undefined) {
      $.ajax({
        url: `${apiUrl}/block/${blockId}`,
        method: 'DELETE',
        success: (data) => {
          cb(true);
        },
        complete: () => {},
        error: () => {}
      });
    }
  }
}