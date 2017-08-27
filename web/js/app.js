var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

document.addEventListener("DOMContentLoaded", function (e) {
    loadTemplate(window.templateId);
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
$('.template').on('click', function (evebt) {
    var templateId = evebt.currentTarget.attributes[1].value;
    var checkIcon = $(evebt.target).parent().find('.check-icon');

    $('.templates-list .check-icon').each(function () {
        $(this).css('display', 'none');
    });
    $(checkIcon).css('display', 'block');

    loadTemplate(templateId);
});
$('.themes-listitem').on('click', function (evebt) {
    var themeSource = evebt.currentTarget.attributes[1].value;
    var checkIcon = $(evebt.target).parent().find('.check-icon');

    $('.themes-list').find('.check-icon').each(function () {
        $(this).css('display', 'none');
    });

    $(checkIcon).css('display', 'block');

    loadTheme(themeSource);
});
var loadTheme = function loadTheme(themeSource) {
    $('head').append('<link href="/templates/default/' + themeSource + '" rel="stylesheet">');
};
var loadTemplate = function loadTemplate(templateId) {
    activateLoader();
    API.getCv(templateId, function (data) {
        var domParser = new DOMParser();
        var template = domParser.parseFromString(data, "text/html");
        var templateHtml = template.getElementById('main-wrap');
        var templateStyles = template.getElementsByTagName('link');

        //Add template HTML
        $('#template').html(templateHtml);

        //Add template styles
        $('head').append(templateStyles);

        if (window.isEditing) {
            prepareToEditTemplate();
        }

        setPlaceholders();

        //Add timeout to remove twitches after loading template
        setTimeout(function () {
            $('#loader').removeClass('active');
        }, 1000);
    });
};

var setPlaceholders = function setPlaceholders() {
    var placeholders = $('body').find("[data-placeholder]");

    for (var _i = 0; _i < placeholders.length; _i++) {
        var element = $(placeholders[_i]);
        var value = element.data('placeholder');
        element.html(value);
    }
};

var prepareToEditTemplate = function prepareToEditTemplate() {
    var statusBarDom = $('#status-bar');
    window.statusBar = new StatusBar(statusBarDom);

    //Setup zones
    var zones = $('[data-zone-block-types]');
    for (var _i2 = 0; _i2 < zones.length; _i2++) {
        new Zone(zones[_i2]);
    }

    //Setup blocks
    var blocks = $('[data-block-id]');
    for (var _i3 = 0; _i3 < blocks.length; _i3++) {
        new Block(blocks[_i3]);
    }
};

var activateLoader = function activateLoader() {
    var loader = document.createElement('div');
    var loaderIcon = document.createElement('div');

    loader.setAttribute('id', 'loader');
    loader.setAttribute('class', 'active');
    loaderIcon.setAttribute('class', 'signal');

    loader.append(loaderIcon);

    $('#template').append(loader);
};

var StatusBar = function () {
    function StatusBar(element) {
        _classCallCheck(this, StatusBar);

        this._element = element[0];

        var _timer;

        var closeButton = this._element.querySelector('.close');
        closeButton.addEventListener('click', this._hide.bind(this), false);

        this.undoButton = this._element.querySelector('.action');

        this._isActive = false;
    }

    _createClass(StatusBar, [{
        key: 'showMessage',
        value: function showMessage(message) {
            var _this = this;

            this._element.classList.remove('is-error');

            var messageEl = this._element.querySelector('.message');
            messageEl.innerHTML = message;
            this._show();

            return new Promise(function (resolve, reject) {
                _timer = setTimeout(function () {
                    _this._hide();
                    resolve();
                }, 5000);

                Rx.Observable.fromEvent(_this.undoButton, 'click').subscribe(function () {
                    window.clearTimeout(_timer);
                    _this._hide();
                    reject();
                });
            });
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
        key: '_show',
        value: function _show() {
            this._element.classList.add('is-active');
            this._isActive = true;
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

            var cancelButton = document.createElement('a');
            cancelButton.classList.add('active', 'cancel-button');
            cancelButton.innerHTML = 'Cancel';
            cancelButton.addEventListener('click', this._hideAddBlockList.bind(this), false);
            blockWrapper.appendChild(cancelButton);

            this._addBlock = blockWrapper;
        }
    }, {
        key: '_showAddBlockList',
        value: function _showAddBlockList() {
            this._addBlock.classList.add('is-active');
        }
    }, {
        key: '_hideAddBlockList',
        value: function _hideAddBlockList() {
            this._addBlock.classList.remove('is-active');
        }
    }, {
        key: '_addNewBlock',
        value: function _addNewBlock(zoneName, type) {
            this._addBlock.classList.remove('is-active');

            API.getBlock(type.type, function (block) {
                var newBlock = $(block)[0];

                $('[data-zone="' + zoneName + '"]').find('.add-block').before(newBlock);
                new Block(newBlock);

                newBlock.firstChild.classList.add('is-editing');

                var editableElements = newBlock.querySelectorAll('[data-key]');
                for (var _i4 = 0; _i4 < editableElements.length; _i4++) {
                    var key = editableElements[_i4].getAttribute('data-key');
                    if (['blocks'].indexOf(key) === -1) {
                        editableElements[_i4].setAttribute('contenteditable', true);
                    }
                }

                var sliders = $(newBlock).find('.skills');
                for (var _i5 = 0; _i5 < sliders.length; _i5++) {
                    if ($(sliders[_i5]).parent().find('.slider').length === 0) {
                        var slider = document.createElement('div');
                        slider.classList.add('slider');
                        var value = $(sliders[_i5]).parent('.skills-group').attr('data-value');
                        $(slider).slider({
                            range: 'max',
                            min: 0,
                            max: 10,
                            value: value,
                            slide: function slide(event, ui) {
                                var value = ui.value;
                                $(this).parent(".skills-group").attr("data-value", ui.value);
                            }
                        });

                        $(sliders[_i5]).after(slider);
                    }
                }

                window.statusBar.showMessage('You have just added ' + type.name + ' block').then(function () {
                    // @TODO fix this when API will be done.
                }).catch(function (reason) {});
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

                    for (var _i6 = 0; _i6 < zones.length; _i6++) {
                        var types = [];
                        if ($(zones[_i6]).data('zoneBlockTypes') !== undefined) {
                            types = $(zones[_i6]).data('zoneBlockTypes').map(function (obj) {
                                return obj.type;
                            });
                        }

                        if (types.indexOf(type) === -1 && $(zones[_i6]).hasClass('ui-sortable')) {
                            $(zones[_i6]).sortable('disable');
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
                var cancelButton = document.createElement('button');
                cancelButton.classList.add('cancel');
                cancelButton.innerHTML = 'Cancel';
                cancelButton.addEventListener('click', this.cancel.bind(this), false);
                blockActionsWrapper.appendChild(cancelButton);
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
            for (var _i7 = 0; _i7 < placeholders.length; _i7++) {
                var el = $(placeholders[_i7])[0];
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
                for (var _i8 = 0; _i8 < editableElements.length; _i8++) {
                    var key = editableElements[_i8].getAttribute('data-key');
                    if (['skill', 'blocks'].indexOf(key) === -1) {
                        editableElements[_i8].setAttribute('contenteditable', true);
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
            for (var _i9 = 0; _i9 < editableElements.length; _i9++) {
                var key = editableElements[_i9].getAttribute('data-key');
                if (['blocks'].indexOf(key) === -1) {
                    editableElements[_i9].setAttribute('contenteditable', true);
                }
            }

            var sliders = $(this._element).find('.skills');
            for (var _i10 = 0; _i10 < sliders.length; _i10++) {
                if ($(sliders[_i10]).parent().find('.slider').length === 0) {
                    var slider = document.createElement('div');
                    slider.classList.add('slider');
                    var value = $(sliders[_i10]).parent('.skills-group').attr('data-value');
                    $(slider).slider({
                        range: 'max',
                        min: 0,
                        max: 10,
                        value: value,
                        slide: function slide(event, ui) {
                            var value = ui.value;
                            $(this).parent(".skills-group").attr("data-value", ui.value);
                        }
                    });

                    $(sliders[_i10]).after(slider);
                }
            }

            this._toggleEditing();
        }
    }, {
        key: 'cancel',
        value: function cancel() {
            var editableElements = this._element.querySelectorAll('[data-key]');

            for (var z = 0; z < editableElements.length; z++) {
                if (['blocks'].indexOf(editableElements[z].getAttribute('data-key')) === -1) {
                    editableElements[z].setAttribute('contenteditable', false);
                }
            }

            this._toggleEditing();
            loadTemplate(window.templateId);
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

            for (var _i11 = 0; _i11 < editableElements.length; _i11++) {

                if (editableElements[_i11].getAttribute('data-key') !== 'blocks') {
                    if (data['fields']['blocks'] !== undefined) {
                        var keysCount = $(this._element).find('[data-key="blocks"]').find('[data-key]');
                        var sameKeysCount = $(this._element).find('[data-key="blocks"]').find('[data-key="' + keysCount[0].getAttribute('data-key') + '"]');

                        var obj = {};
                        for (var j = 0; j < keysCount.length / sameKeysCount.length; j++) {
                            var dataValue = editableElements[_i11 + j].getAttribute('data-value') ? editableElements[_i11 + j].getAttribute('data-value') : editableElements[_i11 + j].innerHTML;
                            var dataKey = editableElements[_i11 + j].getAttribute('data-key');
                            obj[dataKey] = dataValue;
                        }

                        _i11 += keysCount.length / sameKeysCount.length - 1;

                        data['fields']['blocks'].push(obj);
                    } else {
                        data['fields'][editableElements[_i11].getAttribute('data-key')] = editableElements[_i11].innerHTML;
                    }
                } else {
                    data['fields']['blocks'] = [];
                }
            }

            for (var z = 0; z < editableElements.length; z++) {
                if (['blocks'].indexOf(editableElements[z].getAttribute('data-key')) === -1) {
                    editableElements[z].setAttribute('contenteditable', false);
                }
            }

            API.saveBlock(data, function () {});

            this._toggleEditing();
        }
    }, {
        key: 'delete',
        value: function _delete() {
            var blockId = this._element.getAttribute('data-block-id');
            var element = this._element;
            element.classList.add('hidden');
            window.statusBar.showMessage('You have just deleted block').then(function () {
                API.deleteBlock(blockId, function () {
                    element.parentNode.removeChild(element);
                });
            }).catch(function (reason) {
                element.classList.remove('hidden');
            });
        }
    }, {
        key: 'deleteMicroBlock',
        value: function deleteMicroBlock(e) {
            $(e.target).parent().parent().detach();
        }
    }]);

    return Block;
}();

var API = {

    getCv: function getCv(templateId, cb) {
        $.ajax({
            url: '' + window.templateUrl + templateId + '/template',
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
    },

    deleteBlock: function deleteBlock(blockId, cb) {
        if (blockId !== undefined) {
            $.ajax({
                url: apiUrl + '/block/' + blockId,
                method: 'DELETE',
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
