"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

document.addEventListener("DOMContentLoaded", function (e) {
  loadTemplate();
});

var loadTemplate = function loadTemplate() {
  $.ajax({
    url: window.templateUrl,
    success: function success(data) {
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

      //Add timeout to remove twitches
      setTimeout(function () {
        $('#loader').removeClass('active');
      }, 1000);
    },

    complete: function complete() {},

    error: function error() {}
  });
};

var prepareToEditTemplate = function prepareToEditTemplate() {
  var statusBar = new StatusBar(document.querySelector('#status-bar'));
  window.statusBar = statusBar;

  var zones = document.querySelectorAll('[data-zone]');
  var addBlocks = [];
  for (var i = 0; i < zones.length; i++) {
    addBlocks.push(new AddBlock(zones[i]));
  }

  var blocks = document.querySelectorAll('[data-block-id]');
  var EditableBlocks = [];

  for (var _i = 0; _i < blocks.length; _i++) {
    EditableBlocks.push(new EditableBlock(blocks[_i]));
  }
};

var EditableBlock = function () {
  function EditableBlock(element) {
    _classCallCheck(this, EditableBlock);

    this._element = element;
    this._isEditing = false;
    this._hasAddMicroBlock = false;

    this._createControls();
  }

  _createClass(EditableBlock, [{
    key: "_createControls",
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
        moveButton.classList.add('move');
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
    key: "_toggleEditing",
    value: function _toggleEditing() {
      this._element.querySelector('.editable-block').classList.toggle('is-editing');
      this._isEditing = !this._isEditing;
    }
  }, {
    key: "edit",
    value: function edit() {
      var editableElements = this._element.querySelectorAll('[data-key]');
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (var _iterator = editableElements[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
          var el = _step.value;

          var key = el.getAttribute('data-key');
          if (['skill', 'blocks'].indexOf(key) === -1) {
            el.setAttribute('contenteditable', true);
          }

          if ('blocks' === key && !this._hasAddMicroBlock) {
            this._hasAddMicroBlock = true;
            new AddMicroBlock(el);
          }
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

      this._toggleEditing();
    }
  }, {
    key: "save",
    value: function save() {
      var editableElements = this._element.querySelectorAll('[data-key]');
      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = editableElements[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          var el = _step2.value;

          if (['skill', 'blocks'].indexOf(el.getAttribute('data-key')) === -1) {
            el.setAttribute('contenteditable', false);
          }
        }
      } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion2 && _iterator2.return) {
            _iterator2.return();
          }
        } finally {
          if (_didIteratorError2) {
            throw _iteratorError2;
          }
        }
      }

      this._toggleEditing();

      //Log saving info
      var _iteratorNormalCompletion3 = true;
      var _didIteratorError3 = false;
      var _iteratorError3 = undefined;

      try {
        for (var _iterator3 = editableElements[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
          var _el = _step3.value;

          //Disable logging block html data
          if (_el.getAttribute('data-key') !== 'blocks') {
            if (JSON.parse(_el.getAttribute('data-is-child')) === true) {
              console.log(_defineProperty({}, _el.getAttribute('data-key'), _el.innerHTML));
            } else {
              console.log(_defineProperty({}, _el.getAttribute('data-key'), _el.innerHTML));
            }
          }
        }
      } catch (err) {
        _didIteratorError3 = true;
        _iteratorError3 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion3 && _iterator3.return) {
            _iterator3.return();
          }
        } finally {
          if (_didIteratorError3) {
            throw _iteratorError3;
          }
        }
      }
    }
  }, {
    key: "delete",
    value: function _delete() {
      this._element.parentNode.removeChild(this._element);

      window.statusBar.showMessage('You have just deleted block');
    }
  }]);

  return EditableBlock;
}();

;

var StatusBar = function () {
  function StatusBar(element) {
    _classCallCheck(this, StatusBar);

    this._element = element;

    var closeButton = this._element.querySelector('.close');
    closeButton.addEventListener('click', this._hide.bind(this), false);

    this._isActive = false;
    this._animationTimeoutId = null;
  }

  _createClass(StatusBar, [{
    key: "showMessage",
    value: function showMessage(message) {
      this._element.classList.remove('is-error');

      var messageEl = this._element.querySelector('.message');
      messageEl.innerHTML = message;
      // this._element.querySelector('.action')

      this._show();
    }
  }, {
    key: "showError",
    value: function showError(error) {
      this._element.classList.add('is-error');

      var messageEl = this._element.querySelector('.message');
      messageEl.innerHTML = error;

      this._show();
    }
  }, {
    key: "_showBar",
    value: function _showBar() {
      this._element.classList.add('is-active');
      this._isActive = true;

      clearTimeout(this._animationTimeoutId);
      this._animationTimeoutId = setTimeout(this._hide.bind(this), 5000);
    }
  }, {
    key: "_show",
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
    key: "_hide",
    value: function _hide() {
      this._element.classList.remove('is-active');
      this._isActive = false;
    }
  }]);

  return StatusBar;
}();

;

var AddBlock = function () {
  function AddBlock(zone) {
    _classCallCheck(this, AddBlock);

    this._element = null;

    this._createAddBlock(zone);
  }

  _createClass(AddBlock, [{
    key: "_createAddBlock",
    value: function _createAddBlock(zone) {
      var _this2 = this;

      var zoneName = zone.getAttribute('data-zone');
      var zoneTypes = JSON.parse(zone.getAttribute('data-zone-block-types'));

      var blockWrapper = document.createElement('div');
      blockWrapper.classList.add('add-block');

      var button = document.createElement('button');
      button.innerHTML = 'Add block';
      button.addEventListener('click', this._showList.bind(this), false);
      blockWrapper.appendChild(button);

      var zonesList = document.createElement('ul');
      zonesList.classList.add('add-block-items', 'clearfix');
      blockWrapper.appendChild(zonesList);

      var _iteratorNormalCompletion4 = true;
      var _didIteratorError4 = false;
      var _iteratorError4 = undefined;

      try {
        var _loop = function _loop() {
          var type = _step4.value;

          var listItem = document.createElement('li');
          listItem.addEventListener('click', function () {
            return _this2._addBlock(zoneName, type);
          }, false);

          var listImg = document.createElement('img');
          listImg.classList.add('icon');
          listImg.setAttribute('src', window.location.origin + "/img/add-block-" + type.type + ".png");
          listItem.appendChild(listImg);

          var listName = document.createElement('span');
          listName.classList.add('title');
          listName.innerHTML = type.name;
          listItem.appendChild(listName);

          zonesList.appendChild(listItem);
        };

        for (var _iterator4 = zoneTypes[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
          _loop();
        }
      } catch (err) {
        _didIteratorError4 = true;
        _iteratorError4 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion4 && _iterator4.return) {
            _iterator4.return();
          }
        } finally {
          if (_didIteratorError4) {
            throw _iteratorError4;
          }
        }
      }

      zone.appendChild(blockWrapper);

      this._element = blockWrapper;
    }
  }, {
    key: "_showList",
    value: function _showList() {
      this._element.classList.add('is-active');
    }
  }, {
    key: "_addBlock",
    value: function _addBlock(zoneName, type) {
      this._element.classList.remove('is-active');

      window.statusBar.showMessage("You have just added " + type.name + " block");

      console.log(zoneName, type);
    }
  }]);

  return AddBlock;
}();

;

var AddMicroBlock = function () {
  function AddMicroBlock(microBlockZone) {
    _classCallCheck(this, AddMicroBlock);

    this._element = null;

    this._createAddMicroBlock(microBlockZone);
  }

  _createClass(AddMicroBlock, [{
    key: "_createAddMicroBlock",
    value: function _createAddMicroBlock(microBlockZone) {
      var blockWrapper = document.createElement('div');
      blockWrapper.classList.add('add-micro-block');

      var button = document.createElement('button');
      button.innerHTML = 'Add Micro block';
      blockWrapper.appendChild(button);

      microBlockZone.parentNode.appendChild(blockWrapper);

      this._element = blockWrapper;
      console.log(this._element);
    }
  }]);

  return AddMicroBlock;
}();
//# sourceMappingURL=showTemplate.js.map
