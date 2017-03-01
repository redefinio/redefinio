var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

document.addEventListener("DOMContentLoaded", function (e) {
  loadTemplate();
});

$('.edit-url-btn').on('click', function () {
  var copyTextarea = document.querySelector('.edit-url');
  copyTextarea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';

    $('.text-copied').addClass('active');
    setTimeout(function () {
      $('.text-copied').removeClass('active');
    }, 500);
  } catch (err) {}
});

var loadTemplate = function loadTemplate() {
  API.getCv(function (data) {
    var domParser = new DOMParser();
    var template = domParser.parseFromString(data, "text/html");
    var templateHtml = template.getElementById('main-wrap');
    var templateStyles = template.getElementsByTagName('link');

    //Add template HTML
    $('#template').prepend(templateHtml);

    //Add template styles
    $('head').append(templateStyles);

    if (window.isEditing) {
      prepareToEditTemplate();
    }

    //Add timeout to remove twitches after loading template
    setTimeout(function () {
      $('#loader').removeClass('active');
    }, 1000);
  });
};

var prepareToEditTemplate = function prepareToEditTemplate() {
  var statusBarDom = $('#status-bar');
  window.statusBar = new StatusBar(statusBarDom);

  //Setup zones
  var zones = $('[data-zone-block-types]');
  for (var _i = 0; _i < zones.length; _i++) {
    new Zone(zones[_i]);
  }

  //Setup blocks
  var blocks = $('[data-block-id]');
  for (var _i2 = 0; _i2 < blocks.length; _i2++) {
    new Block(blocks[_i2]);
  }
};

var StatusBar = function () {
  function StatusBar(element) {
    _classCallCheck(this, StatusBar);

    this._element = element[0];

    var closeButton = this._element.querySelector('.close');
    closeButton.addEventListener('click', this._hide.bind(this), false);

    this._isActive = false;
    this._animationTimeoutId = null;
  }

  _createClass(StatusBar, [{
    key: 'showMessage',
    value: function showMessage(message) {
      this._element.classList.remove('is-error');

      var messageEl = this._element.querySelector('.message');
      messageEl.innerHTML = message;
      // this._element.querySelector('.action')

      this._show();
    }
  }, {
    key: 'showError',
    value: function showError(error) {
      this._element.classList.add('is-error');

      var messageEl = this._element.querySelector('.message');
      messageEl.innerHTML = error;

      this._show();
    }
  }, {
    key: '_showBar',
    value: function _showBar() {
      this._element.classList.add('is-active');
      this._isActive = true;

      clearTimeout(this._animationTimeoutId);
      this._animationTimeoutId = setTimeout(this._hide.bind(this), 5000);
    }
  }, {
    key: '_show',
    value: function _show() {
      var _this = this;

      if (this._isActive) {
        new Promise(function (resolve, reject) {
          _this._hide();
          setTimeout(resolve, 250);
        }).then(this._showBar.bind(this));
      } else {
        this._showBar();
      }
    }
  }, {
    key: '_hide',
    value: function _hide() {
      this._element.classList.remove('is-active');
      this._isActive = false;
    }
  }]);

  return StatusBar;
}();

var Zone = function () {
  function Zone(zone) {
    _classCallCheck(this, Zone);

    this._addBlock = null;
    this._element = zone;

    this._createAddBlock();
    this._enableDragNDrop();
  }

  _createClass(Zone, [{
    key: '_createAddBlock',
    value: function _createAddBlock() {
      var _this2 = this;

      var zoneName = this._element.getAttribute('data-zone');
      var zoneTypes = JSON.parse(this._element.getAttribute('data-zone-block-types'));

      var blockWrapper = document.createElement('div');
      blockWrapper.classList.add('add-block');

      var button = document.createElement('button');
      button.innerHTML = 'Add block';
      button.addEventListener('click', this._showAddBlockList.bind(this), false);
      blockWrapper.appendChild(button);

      var zonesList = document.createElement('ul');
      zonesList.classList.add('add-block-items', 'clearfix');
      blockWrapper.appendChild(zonesList);

      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        var _loop = function _loop() {
          var type = _step.value;

          var listItem = document.createElement('li');
          listItem.addEventListener('click', function () {
            return _this2._addNewBlock(zoneName, type);
          }, false);

          var listImg = document.createElement('img');
          listImg.classList.add('icon');
          listImg.setAttribute('src', window.location.origin + '/img/add-block-' + type.type + '.png');
          listItem.appendChild(listImg);

          var listName = document.createElement('span');
          listName.classList.add('title');
          listName.innerHTML = type.name;
          listItem.appendChild(listName);

          zonesList.appendChild(listItem);
        };

        for (var _iterator = zoneTypes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          _loop();
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator.return) {
            _iterator.return();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }

      this._element.appendChild(blockWrapper);
      this._addBlock = blockWrapper;
    }
  }, {
    key: '_showAddBlockList',
    value: function _showAddBlockList() {
      this._addBlock.classList.add('is-active');
    }
  }, {
    key: '_addNewBlock',
    value: function _addNewBlock(zoneName, type) {
      this._addBlock.classList.remove('is-active');

      API.getBlock(type.type, function (block) {
        var newBlock = $(block)[0];

        $('[data-zone="' + zoneName + '"]').find('.add-block').before(newBlock);
        new Block(newBlock);

        window.statusBar.showMessage('You have just added ' + type.name + ' block');
      });
    }
  }, {
    key: '_enableDragNDrop',
    value: function _enableDragNDrop() {
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
        activate: function activate(e, ui) {},
        beforeStop: function beforeStop(e, ui) {},
        change: function change(e, ui) {},
        create: function create(e, ui) {},
        deactivate: function deactivate(e, ui) {},
        out: function out(e, ui) {},
        over: function over(e, ui) {},
        receive: function receive(e, ui) {
          var type = $(ui.item).data('blockType');
          var types = $(e.target).data('zoneBlockTypes').map(function (obj) {
            return obj.type;
          });

          if (types.indexOf(type) === -1) {
            $(ui.sender).sortable('cancel');
          }

          if ($(zones[i]).hasClass('ui-sortable')) {
            $('[data-zone]').sortable('enable');
          }
        },
        remove: function remove(e, ui) {},
        sort: function sort(e, ui) {},
        start: function start(e, ui) {
          var type = $(ui.item).data('blockType');
          var zones = $('[data-zone]');

          for (var _i3 = 0; _i3 < zones.length; _i3++) {
            var types = [];
            if ($(zones[_i3]).data('zoneBlockTypes') !== undefined) {
              types = $(zones[_i3]).data('zoneBlockTypes').map(function (obj) {
                return obj.type;
              });
            }

            if (types.indexOf(type) === -1 && $(zones[_i3]).hasClass('ui-sortable')) {
              $(zones[_i3]).sortable('disable');
            }
          }
        },
        stop: function stop(e, ui) {},
        update: function update(e, ui) {}
      });
    }
  }]);

  return Zone;
}();

var Block = function () {
  function Block(block) {
    _classCallCheck(this, Block);

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

  _createClass(Block, [{
    key: '_createControls',
    value: function _createControls() {
      var blockWrapper = document.createElement('div');
      blockWrapper.classList.add('editable-block');

      var blockActionsWrapper = document.createElement('div');
      blockActionsWrapper.classList.add('block-actions');
      blockWrapper.appendChild(blockActionsWrapper);

      var itemPlaceholder = document.createElement('div');
      itemPlaceholder.classList.add('item-placeholder');
      itemPlaceholder.innerHTML = this._element.innerHTML;
      blockWrapper.appendChild(itemPlaceholder);

      if (JSON.parse(this._element.getAttribute('data-is-draggable')) === true) {
        var moveButton = document.createElement('button');
        moveButton.classList.add('move', 'move-block');
        blockActionsWrapper.appendChild(moveButton);
      }

      if (JSON.parse(this._element.getAttribute('data-is-editable')) === true) {
        var editButton = document.createElement('button');
        editButton.classList.add('edit');
        editButton.innerHTML = 'Edit';
        editButton.addEventListener('click', this.edit.bind(this), false);
        blockActionsWrapper.appendChild(editButton);
      }

      if (JSON.parse(this._element.getAttribute('data-is-deletable')) === true) {
        var deleteButton = document.createElement('button');
        deleteButton.classList.add('delete');
        deleteButton.innerHTML = 'Delete';
        deleteButton.addEventListener('click', this.delete.bind(this), false);
        blockActionsWrapper.appendChild(deleteButton);
      }

      if (JSON.parse(this._element.getAttribute('data-is-editable')) === true) {
        var saveButton = document.createElement('button');
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
  }, {
    key: '_fixPlaceholders',
    value: function _fixPlaceholders() {
      var placeholders = $(this._element).find('[data-placeholder]');
      for (var _i4 = 0; _i4 < placeholders.length; _i4++) {
        var el = $(placeholders[_i4])[0];
        var placeholder = el.dataset.placeholder;
        if (el.innerHTML.indexOf('{{') > -1) {
          $(el).html(placeholder);
        }
      }
    }
  }, {
    key: '_createMicroBlockControls',
    value: function _createMicroBlockControls() {
      $('[data-key="blocks"] > div, [data-key="blocks"] > li').addClass('editable-micro-block');

      var blockActionsWrapper = document.createElement('div');
      blockActionsWrapper.classList.add('block-actions');

      var moveButton = document.createElement('button');
      moveButton.classList.add('move', 'move-micro-block');
      blockActionsWrapper.appendChild(moveButton);

      var deleteButton = document.createElement('button');
      deleteButton.classList.add('delete');
      deleteButton.innerHTML = 'Delete';
      $(deleteButton).on('click', this.deleteMicroBlock.bind(this));
      blockActionsWrapper.appendChild(deleteButton);

      $('[data-key="blocks"] > div, [data-key="blocks"] > li').remove('.block-actions');
      $('[data-key="blocks"] > div, [data-key="blocks"] > li').append(blockActionsWrapper);
    }
  }, {
    key: '_enableMicroBlockDragNDrop',
    value: function _enableMicroBlockDragNDrop() {
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
        activate: function activate(e, ui) {},
        beforeStop: function beforeStop(e, ui) {},
        change: function change(e, ui) {},
        create: function create(e, ui) {},
        deactivate: function deactivate(e, ui) {},
        out: function out(e, ui) {},
        over: function over(e, ui) {},
        receive: function receive(e, ui) {},
        remove: function remove(e, ui) {},
        sort: function sort(e, ui) {},
        start: function start(e, ui) {},
        stop: function stop(e, ui) {},
        update: function update(e, ui) {}
      });
    }
  }, {
    key: '_createAddBlock',
    value: function _createAddBlock() {
      var blockWrapper = document.createElement('div');
      blockWrapper.classList.add('add-micro-block');

      var button = document.createElement('button');
      button.innerHTML = 'Add Micro block';
      button.addEventListener('click', this._addMicroBlock.bind(this), false);
      blockWrapper.appendChild(button);
      $(this._element).find('.editable-block').append(blockWrapper);
    }
  }, {
    key: '_addMicroBlock',
    value: function _addMicroBlock() {
      var _this3 = this;

      API.getBlock(this._childBlockType, function (block) {
        $(_this3._element).find('[data-key="blocks"]').append(block);
        _this3._createMicroBlockControls();
        _this3._fixPlaceholders();

        //TODO: refactor edit function
        var editableElements = _this3._element.querySelectorAll('[data-key]');
        for (var _i5 = 0; _i5 < editableElements.length; _i5++) {
          var key = editableElements[_i5].getAttribute('data-key');
          if (['skill', 'blocks'].indexOf(key) === -1) {
            editableElements[_i5].setAttribute('contenteditable', true);
          }
        }
      });
    }
  }, {
    key: '_toggleEditing',
    value: function _toggleEditing() {
      this._element.querySelector('.editable-block').classList.toggle('is-editing');
      this._isEditing = !this._isEditing;
    }
  }, {
    key: 'edit',
    value: function edit() {
      var editableElements = this._element.querySelectorAll('[data-key]');
      for (var _i6 = 0; _i6 < editableElements.length; _i6++) {
        var key = editableElements[_i6].getAttribute('data-key');
        if (['blocks'].indexOf(key) === -1) {
          editableElements[_i6].setAttribute('contenteditable', true);
        }
      }

      // console.log($(this._element).find('.skills'))


      var sliders = $(this._element).find('.skills'); //.after(slider);
      for (var _i7 = 0; _i7 < sliders.length; _i7++) {
        // console.log($(sliders[i]).parent().find('.slider').length)
        if ($(sliders[_i7]).parent().find('.slider').length === 0) {
          var slider = document.createElement('div');
          slider.classList.add('slider');
          $(slider).slider({
            range: 'max',
            min: 0,
            max: 10
          });
          $(sliders[_i7]).after(slider);
        }
      }

      this._toggleEditing();
    }
  }, {
    key: 'save',
    value: function save() {
      var editableElements = this._element.querySelectorAll('[data-key]');

      //Data saving
      var counter = 0;
      var data = {};
      data['blockId'] = this._element.dataset.blockId || 0;
      data['blockType'] = this._element.dataset.blockType;
      data['zone'] = $(this._element).parent().data('zone');
      data['fields'] = {};
      for (var _i8 = 0; _i8 < editableElements.length; _i8++) {
        if (['blocks'].indexOf(editableElements[_i8].getAttribute('data-key')) === -1) {
          editableElements[_i8].setAttribute('contenteditable', false);
        }

        if (editableElements[_i8].getAttribute('data-key') !== 'blocks') {
          if (data['fields']['blocks'] !== undefined) {
            var keysCount = $(this._element).find('[data-key="blocks"]').find('[data-key]');
            var sameKeysCount = $(this._element).find('[data-key="blocks"]').find('[data-key="' + keysCount[0].getAttribute('data-key') + '"]');
            // console.log(keysCount.length, sameKeysCount.length);
            var obj = {};
            for (var j = 0; j < keysCount.length / sameKeysCount.length; j++) {
              obj[editableElements[_i8 + j].getAttribute('data-key')] = editableElements[_i8 + j].innerHTML;
              // console.log(i, j);
            }

            _i8 += keysCount.length / sameKeysCount.length - 1;
            // console.log(obj);
            data['fields']['blocks'].push(obj);
          } else {
            data['fields'][editableElements[_i8].getAttribute('data-key')] = editableElements[_i8].innerHTML;
          }
        } else {
          data['fields']['blocks'] = [];
        }
      }

      console.log(data);
      API.saveBlock(data, function () {});

      this._toggleEditing();
    }
  }, {
    key: 'delete',
    value: function _delete() {
      this._element.parentNode.removeChild(this._element);

      window.statusBar.showMessage('You have just deleted block');
    }
  }, {
    key: 'deleteMicroBlock',
    value: function deleteMicroBlock(e) {
      $(e.target).parent().parent().detach();
    }
  }]);

  return Block;
}();

;

var API = {
  getCv: function getCv(cb) {
    $.ajax({
      url: window.templateUrl,
      success: function success(data) {
        cb(data);
      },
      complete: function complete() {},
      error: function error() {}
    });
  },

  getBlock: function getBlock(type, cb) {
    $.ajax({
      url: apiUrl + '/block/' + window.templateId + '/' + type,
      success: function success(data) {
        var block = decodeURIComponent(JSON.parse(data).data).replace(/\+/g, ' ');

        cb(block);
      },
      complete: function complete() {},
      error: function error() {}
    });
  },

  saveBlock: function saveBlock(block, cb) {
    block.cvId = cvId;

    if (block.blockId !== 0) {
      $.ajax({
        url: apiUrl + '/block/' + window.cvId + '/' + block.zone + '/' + block.blockId,
        method: 'PUT',
        data: block,
        success: function success(data) {
          cb(true);
        },
        complete: function complete() {},
        error: function error() {}
      });
    } else {
      $.ajax({
        url: apiUrl + '/block/' + window.cvId + '/' + block.zone,
        method: 'POST',
        data: block,
        success: function success(data) {
          cb(true);
        },
        complete: function complete() {},
        error: function error() {}
      });
    }
  }
};
//# sourceMappingURL=app.js.map
