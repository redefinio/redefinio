var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var _isEditing = false;

document.addEventListener("DOMContentLoaded", function (e) {
    loadTemplate(window.templateId);

    if (window.location.hash === "#published" && document.referrer === editUrl) {
        $('.container-message').css('display', 'block');
    }
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
    if (_isEditing) {
        $('#myModal').modal('show');
        $('#myModal').on('click', 'button', function (event) {
            if (event.currentTarget.getAttribute('data-action') === 'cancel') {
                $('#myModal').modal('hide');
            } else {
                _isEditing = false;
                loadTemplate(templateId);
                setCheckIcon('.templates-list .check-icon', checkIcon);
                $('#myModal').modal('hide');
            }
        });
    } else {
        loadTemplate(templateId);
        setCheckIcon('.templates-list .check-icon', checkIcon);
    }
});

$('#publish-button').on('click', function (event) {
    API.publishTemplate(function (data) {
        _isPublished = true;
        window.location.href = templateUrl + "#published";
    });
});
$('.themes-list').on('click', '.themes-listitem', function (evebt) {
    var themeSource = $(evebt.currentTarget).data("themeSource");
    var checkIcon = $(evebt.target).parent().find('.check-icon');
    var themeId = $(evebt.currentTarget).data("themeId");

    $('.themes-list').find('.check-icon').each(function () {
        $(this).css('display', 'none');
    });

    API.updateTheme(themeId, function (data) {});

    $(checkIcon).css('display', 'block');
    loadTheme(themeSource);
});

var loadTheme = function loadTheme(themeSource) {
    $('head').append("<link href=\"/templates/" + themeSource + "\" rel=\"stylesheet\">");
};
var loadTemplate = function loadTemplate(templateId) {
    window.templateId = templateId;

    activateLoader();
    API.getCv(templateId, function (data) {
        var domParser = new DOMParser();
        var template = domParser.parseFromString(data.html, "text/html");
        var templateHtml = template.getElementById('main-wrap');
        var templateStyles = template.getElementsByTagName('link');

        //Add template styles
        $('head').find('link').slice(2).remove();
        $('head').append(templateStyles);

        $('.themes-list').html(data.themes);

        //Add timeout to remove twitches after loading template
        setTimeout(function () {
            $('#loader').removeClass('active');
            $('#template').html(templateHtml);

            if (window.isEditing) {
                prepareToEditTemplate();
            }
            setPlaceholders();
        }, 1000);
    });
};

var setPlaceholders = function setPlaceholders() {
    var placeholders = $('body').find("[data-placeholder]");

    for (var i = 0; i < placeholders.length; i++) {
        var element = $(placeholders[i]);
        if (element.html() == "") {
            var value = element.data('placeholder');
            element.html(value);
        }
    }
};

var prepareToEditTemplate = function prepareToEditTemplate() {
    var statusBarDom = $('#status-bar');
    window.statusBar = new StatusBar(statusBarDom);

    //Setup zones
    var zones = $('[data-zone-block-types]');
    for (var i = 0; i < zones.length; i++) {
        new Zone(zones[i]);
    }

    //Setup blocks
    var blocks = $('[data-block-id]');
    for (var _i = 0; _i < blocks.length; _i++) {
        new Block(blocks[_i]);
    }
};

var preapareBlockToEdit = function preapareBlockToEdit(block) {
    new Block(block);
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

var applySliders = function applySliders(element) {
    var sliders = $(element).find('.skills');
    for (var i = 0; i < sliders.length; i++) {
        if ($(sliders[i]).parent().find('.slider').length === 0) {
            var slider = document.createElement('div');
            slider.classList.add('slider');
            var value = $(sliders[i]).parent('.skills-group').attr('data-value');
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

            $(sliders[i]).after(slider);
        }
    }
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
        key: "showMessage",
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
        key: "showError",
        value: function showError(error) {
            this._element.classList.add('is-error');

            var messageEl = this._element.querySelector('.message');
            messageEl.innerHTML = error;

            this._show();
        }
    }, {
        key: "_show",
        value: function _show() {
            $(this._element).show('slow');
            this._isActive = true;
        }
    }, {
        key: "_hide",
        value: function _hide() {
            $(this._element).hide('slow');
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
        key: "_createAddBlock",
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
                    listImg.setAttribute('src', window.location.origin + "/img/add-block-" + type.type + ".png");
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
        key: "_showAddBlockList",
        value: function _showAddBlockList() {
            this._addBlock.classList.add('is-active');
        }
    }, {
        key: "_hideAddBlockList",
        value: function _hideAddBlockList() {
            this._addBlock.classList.remove('is-active');
        }
    }, {
        key: "_addNewBlock",
        value: function _addNewBlock(zoneName, type) {
            this._addBlock.classList.remove('is-active');

            API.getBlock(type.type, function (block) {
                var newBlock = $(block)[0];

                $("[data-zone=\"" + zoneName + "\"]").find('.add-block').before(newBlock);
                new Block(newBlock);

                newBlock.firstChild.classList.add('is-editing');

                var editableElements = newBlock.querySelectorAll('[data-key]');
                for (var i = 0; i < editableElements.length; i++) {
                    var key = editableElements[i].getAttribute('data-key');
                    if (['blocks'].indexOf(key) === -1) {
                        editableElements[i].setAttribute('contenteditable', true);
                    }
                }

                applySliders(newBlock);

                setPlaceholders();

                window.statusBar.showMessage("You have just added " + type.name + " block").then(function () {
                    // @TODO fix this when API will be done.
                }).catch(function (reason) {});
            });
        }
    }, {
        key: "_enableDragNDrop",
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
                },
                remove: function remove(e, ui) {},
                sort: function sort(e, ui) {},
                start: function start(e, ui) {
                    var type = $(ui.item).data('blockType');
                    var zones = $('[data-zone]');

                    for (var i = 0; i < zones.length; i++) {
                        var types = [];
                        var zone = $(zones[i]);
                        if ($(zones[i]).data('zoneBlockTypes') !== undefined) {
                            types = $(zones[i]).data('zoneBlockTypes').map(function (obj) {
                                return obj.type;
                            });
                        }

                        if (types.indexOf(type) === -1 && $(zones[i]).hasClass('ui-sortable')) {
                            $(zones[i]).sortable('disable');
                        }
                    }
                },
                stop: function stop(e, ui) {
                    var zones = $('[data-zone]');

                    for (var i = 0; i < zones.length; i++) {
                        if ($(zones[i]).hasClass('ui-sortable')) {
                            $(zones[i]).sortable('enable');
                        }
                    }
                },
                update: function update(e, ui) {
                    var parent = ui.item.parent('[data-zone]');
                    var wildcard = $(parent).data('zone');
                    var children = parent.find('[data-block-id]');

                    var positions = [];

                    for (var i = 0; i < children.length; i++) {
                        var position = $(children[i]).data('blockId');
                        positions.push(position);
                    }

                    API.sortBlocks(wildcard, positions, function () {});
                }
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
        key: "_createControls",
        value: function _createControls() {
            var blockWrapper = document.createElement('div');
            blockWrapper.classList.add('editable-block');

            var leftblockActionsWrapper = document.createElement('div');
            leftblockActionsWrapper.classList.add('block-actions--left-side');
            blockWrapper.appendChild(leftblockActionsWrapper);

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
                leftblockActionsWrapper.appendChild(cancelButton);
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
        key: "_fixPlaceholders",
        value: function _fixPlaceholders() {
            var placeholders = $(this._element).find('[data-placeholder]');
            for (var i = 0; i < placeholders.length; i++) {
                var el = $(placeholders[i])[0];
                var placeholder = el.dataset.placeholder;
                if (el.innerHTML.indexOf('{{') > -1) {
                    $(el).html(placeholder);
                }
            }
        }
    }, {
        key: "_createMicroBlockControls",
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
        key: "_enableMicroBlockDragNDrop",
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
        key: "_createAddBlock",
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
        key: "_addMicroBlock",
        value: function _addMicroBlock() {
            var _this3 = this;

            API.getBlock(this._childBlockType, function (block) {
                $(_this3._element).find('[data-key="blocks"]').append(block);
                _this3._createMicroBlockControls();
                _this3._fixPlaceholders();

                applySliders(_this3._element);
                setPlaceholders();

                //TODO: refactor edit function
                var editableElements = _this3._element.querySelectorAll('[data-key]');
                for (var i = 0; i < editableElements.length; i++) {
                    var key = editableElements[i].getAttribute('data-key');
                    if (['skill', 'blocks'].indexOf(key) === -1) {
                        editableElements[i].setAttribute('contenteditable', true);
                    }
                }
            });
        }
    }, {
        key: "_toggleEditing",
        value: function _toggleEditing() {
            this._element.querySelector('.editable-block').classList.toggle('is-editing');
            _isEditing = !_isEditing;
        }
    }, {
        key: "edit",
        value: function edit() {
            var editableElements = this._element.querySelectorAll('[data-key]');
            for (var i = 0; i < editableElements.length; i++) {
                var key = editableElements[i].getAttribute('data-key');
                if (['blocks'].indexOf(key) === -1) {
                    editableElements[i].setAttribute('contenteditable', true);
                }

                if (editableElements[i].innerHTML == "" && editableElements[i].classList.contains('hidden')) {
                    editableElements[i].classList.remove('hidden');
                }
            }

            applySliders(this._element);

            this._toggleEditing();
        }
    }, {
        key: "cancel",
        value: function cancel() {
            var _this4 = this;

            var editableElements = this._element.querySelectorAll('[data-key]');
            var blockId = this._element.getAttribute('data-block-id');

            for (var z = 0; z < editableElements.length; z++) {
                if (['blocks'].indexOf(editableElements[z].getAttribute('data-key')) === -1) {
                    editableElements[z].setAttribute('contenteditable', false);
                }
            }

            API.renderBlock(blockId, function (response) {
                _this4._toggleEditing();
                _this4._updateHtml(_this4._element, response.html, true);
            });
        }
    }, {
        key: "save",
        value: function save() {
            var _this5 = this;

            var editableElements = this._element.querySelectorAll('[data-key]');

            //Data saving
            var counter = 0;
            var data = {};
            data['blockId'] = this._element.dataset.blockId || 0;
            data['blockType'] = this._element.dataset.blockType;
            data['zone'] = $(this._element).parent().data('zone');
            data['fields'] = {};

            for (var i = 0; i < editableElements.length; i++) {

                if (editableElements[i].getAttribute('data-key') !== 'blocks') {
                    if (data['fields']['blocks'] !== undefined) {
                        var keysCount = $(this._element).find('[data-key="blocks"]').find('[data-key]');
                        var sameKeysCount = $(this._element).find('[data-key="blocks"]').find('[data-key="' + keysCount[0].getAttribute('data-key') + '"]');

                        var obj = {};
                        for (var j = 0; j < keysCount.length / sameKeysCount.length; j++) {
                            var dataValue = editableElements[i + j].getAttribute('data-value') ? editableElements[i + j].getAttribute('data-value') : editableElements[i + j].innerHTML;
                            var dataKey = editableElements[i + j].getAttribute('data-key');
                            obj[dataKey] = dataValue;
                        }

                        i += keysCount.length / sameKeysCount.length - 1;

                        data['fields']['blocks'].push(obj);
                    } else if (editableElements[i].getAttribute('data-key') == 'photo') {
                        (function () {
                            var files = editableElements[i].files;
                            var key = editableElements[i].getAttribute('data-key');
                            API.uploadPhoto(files, function (response) {
                                data['fields'][key] = response.photo;
                                $('.photo img').attr('src', response.photo);
                            });
                        })();
                    } else {
                        data['fields'][editableElements[i].getAttribute('data-key')] = this.stripTags(editableElements[i].innerHTML);
                    }
                } else {
                    data['fields']['blocks'] = [];
                }
            }

            for (var z = 0; z < editableElements.length; z++) {
                if (['blocks'].indexOf(editableElements[z].getAttribute('data-key')) === -1) {
                    editableElements[z].setAttribute('contenteditable', false);

                    if (editableElements[z].innerHTML == "") {
                        if (editableElements[z].getAttribute('data-required')) {
                            editableElements[z].innerHTML = editableElements[z].getAttribute('data-placeholder');
                        } else {
                            editableElements[z].classList.add('hidden');
                        }
                    }
                }
            }

            API.saveBlock(data, function (response) {
                _this5._toggleEditing();
                _this5._updateHtml(_this5._element, response.html, true);
            });
        }
    }, {
        key: "stripTags",
        value: function stripTags(html) {
            var regex = /(<([^>]+)>)/ig;
            return html.replace(regex, "");
        }
    }, {
        key: "delete",
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
        key: "deleteMicroBlock",
        value: function deleteMicroBlock(e) {
            $(e.target).parent().parent().detach();
        }
    }, {
        key: "_updateHtml",
        value: function _updateHtml(element, html) {
            var editable = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            $(element).html(html);
            setPlaceholders();
            if (editable) {
                preapareBlockToEdit(element);
            }
        }
    }]);

    return Block;
}();

function setCheckIcon(className, checkIcon) {
    $(className).each(function () {
        $(this).css('display', 'none');
    });
    $(checkIcon).css('display', 'block');
}

var API = {

    getCv: function getCv(templateId, cb) {
        var url = apiUrl + "/" + templateId + "/template";
        if (templateId == undefined) {
            url = apiUrl + "/template";
        }
        $.ajax({
            url: url,
            success: function success(data) {
                cb(data);
            },
            complete: function complete() {},
            error: function error() {}
        });
    },

    renderBlock: function renderBlock(blockId, cb) {
        $.ajax({
            url: apiUrl + "/block/" + blockId,
            success: function success(data) {
                cb(data);
            },
            complete: function complete() {},
            error: function error() {}
        });
    },

    updateTheme: function updateTheme(themeId, cb) {
        var data = {
            "themeId": themeId,
            "templateId": window.templateId
        };
        $.ajax({
            url: apiUrl + "/theme",
            method: "PUT",
            data: data,
            success: function success(data) {
                cb(data);
            },
            complete: function complete() {},
            error: function error() {}
        });
    },

    getBlock: function getBlock(type, cb) {
        $.ajax({
            url: apiUrl + "/block/" + window.templateId + "/" + type,
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
        block.templateId = window.templateId;

        if (block.blockId !== 0) {
            $.ajax({
                url: apiUrl + "/block/" + block.zone,
                method: 'PUT',
                data: block,
                success: function success(data) {
                    cb(data);
                },
                complete: function complete() {},
                error: function error() {}
            });
        } else {
            $.ajax({
                url: apiUrl + "/block/" + block.zone,
                method: 'POST',
                data: block,
                success: function success(data) {
                    cb(data);
                },
                complete: function complete() {},
                error: function error() {}
            });
        }
    },

    uploadPhoto: function uploadPhoto(files, cb) {
        var data = new FormData();
        $.each(files, function (key, value) {
            data.append(key, value);
        });

        $.ajax({
            url: apiUrl + "/photo",
            method: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false,
            async: false,
            success: function success(data) {
                cb(data);
            }

        });
    },

    deleteBlock: function deleteBlock(blockId, cb) {
        if (blockId !== undefined) {
            $.ajax({
                url: apiUrl + "/block/" + blockId,
                method: 'DELETE',
                success: function success(data) {
                    cb(true);
                },
                complete: function complete() {},
                error: function error() {}
            });
        }
    },
    sortBlocks: function sortBlocks(wildcard, positions, cb) {
        var payload = {};
        payload['wildcard'] = wildcard;
        payload['positions'] = positions;
        payload['templateId'] = window.templateId;

        $.ajax({
            url: apiUrl + "/zone",
            method: 'PUT',
            data: payload,
            success: function success(data) {
                cb(data);
            },
            error: function error() {},
            complete: function complete() {}

        });
    },
    publishTemplate: function publishTemplate(cb) {
        var payload = {
            'templateId': window.templateId
        };

        $.ajax({
            url: apiUrl + "/publish",
            method: 'PUT',
            data: payload,
            success: function success(data) {
                cb(data);
            },
            error: function error() {},
            complete: function complete() {}
        });
    }
};
//# sourceMappingURL=app.js.map
