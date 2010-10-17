jQuery(document).ready(function() {
    var editProjectForm = $('post');
    if (editProjectForm) {
        editProjectForm.onsubmit = function() {
            for (var editor in tinyMCE.editors) {
                document.getElementById(editor).value = tinyMCE.editors[editor].getContent();
            }
            screenshotsManager.triggerSave();
            //~ return false;
        };
        tinyMCEPreInit.mceInit.height = '200';
        tinyMCEPreInit.mceInit.theme_advanced_resizing = false;
        tinyMCEPreInit.mceInit.editor_selector = "BeastxWPProjectEditor";
        tinyMCE.init(tinyMCEPreInit.mceInit);
        
        tinyMCEPreInit.mceInit.theme_advanced_buttons1 = 'bold, italic, underline, bullist, numlist, link, unlink';
        tinyMCEPreInit.mceInit.editor_selector = "BeastxWPProjectEditorSimple";
        tinyMCE.init(tinyMCEPreInit.mceInit);
    }
});



$ = function(selector) {
    return jQuery('#' + selector)[0];
}

var BeastxAttachmentsManager = function() { this.scriptName = 'BeastxAttachmentManager' };
    
BeastxAttachmentsManager.prototype.init = function(maxAttachments) {
    this.maxAttachments = maxAttachments ? maxAttachments : 1;
    this.container = $('attachmentsContainer');
    this.fileAttacherContainer = $('attachmentsFileAttacherContainer');
    this.idsInput = $('attachments_input');
    this.attachs = [];
    this.appendChild(
        this.fileAttacherContainer,
        this.fileAttacher = New(FileAttacher, [ 'attachments', /^(zip|rar)$/i, BeastxWPProjectTexts.onlyZipFiles ], { onuploadstart: this.caller('onUploadStart'), onuploaddone: this.caller('onUploadDone') })
    );
}

BeastxAttachmentsManager.prototype.onUploadStart = function() {
    this.fileAttacher.setEnabled(false);
}

BeastxAttachmentsManager.prototype.checkMaxLength = function() {
    if (this.maxAttachments && (this.attachs.length >= this.maxAttachments)) {
        this.fileAttacher.setEnabled(false);
        this.fileAttacher.updateStatusText(BeastxWPProjectTexts.fileMaxLength);
    } else {
        this.fileAttacher.setEnabled(true);
        this.fileAttacher.updateStatusText('');
    }
}

BeastxAttachmentsManager.prototype.onUploadDone = function(params) {
    this.fileAttacher.setEnabled(true);
    if (params.error) {
        alert(BeastxWPProjectTexts[params.errorMsg]);
    } else {
        this.addItem(params.id, params.fileName, params.url, params.fileType, params.fileSize);
    }
}

BeastxAttachmentsManager.prototype.addItem = function(id, name, url, type, size) {
    var item = New(BeastxAttachmentItem, [id, name, url, type, size], { onremoveclick: this.caller('onRemoveItemClick') });
    this.attachs.push(item);
    this.appendChild(this.container, item);
    this.updateIdsInputValue();
    this.checkMaxLength();
}

BeastxAttachmentsManager.prototype.onRemoveItemClick = function(item) {
    var newList = [];
    for (var i = 0; i < this.attachs.length; ++i) {
        if (this.attachs[i] != item) {
            newList.push(this.attachs[i]);
        }
    }
    this.removeChild(this.container, item);
    this.attachs = newList;
    this.updateIdsInputValue();
    this.checkMaxLength();
}

BeastxAttachmentsManager.prototype.triggerSave = function() {
    this.updateIdsInputValue();
}

BeastxAttachmentsManager.prototype.updateIdsInputValue = function() {
    this.idsInput.value = this.getValue();
}

BeastxAttachmentsManager.prototype.getValue = function() {
    var value = [];
    for (var i = 0; i < this.attachs.length; ++i) {
        value.push(this.attachs[i].getValue());
    }
    return VAR.serialize(value);
}




var BeastxAttachmentItem = function() {};
    
BeastxAttachmentItem.prototype.init = function(id, name, url, type, size) {
    this.id = id;
    this.name = name;
    this.url = url;
    this.type = type;
    this.size = size;
    this.updateUI();
}

BeastxAttachmentItem.prototype.updateUI = function() {
    this.widget = this.element('div', { 'class': 'AttachmentItem' }, [
        this.element('a', { title: this.url, href: this.url, target: '_blank', 'class': 'AttachmentItemLink' }, [ this.name ]),
        this.element('span', { 'class': 'AttachmentItemMetaData' }, [
            this.getFileSizeText(),
            ' (',
            this.type,
            ')'
        ]),
        this.element('a', { href: '#', onclick: this.caller('onRemoveClick') }, [ BeastxWPProjectTexts.remove ])
    ]);
}

BeastxAttachmentItem.prototype.getFileSizeText = function() {
    var size = '';
    var sizeString = '';
    if (this.size < 1024) {
        sizeString = this.size + ' Bites';
    } else if (this.size >= 1024 && this.size < 1048576) {
        size = (this.size / 1024) + '';
        sizeString = size.substr(0, size.indexOf('.') + 3) + ' KB';
    } else if (this.size >= 1048576) {
        size = (this.size / 1048576) + '';
        sizeString = size.substr(0, size.indexOf('.') + 3) + ' MB';
    } else  {
        sizeString = this.size;
    }
    
    return sizeString;
}

BeastxAttachmentItem.prototype.onRemoveClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm(BeastxWPProjectTexts.areYouSure);
    if (areYouSure) {
        this.dispatchEvent('removeclick', this);
    }
}

BeastxAttachmentItem.prototype.getValue = function() {
    return {
        id: this.id,
        name: this.name,
        url: this.url,
        type: this.type,
        size: this.size
    };
}










var BeastxScreenshotsManager = function() { this.scriptName = 'BeastxScreenshotManager' };
    
BeastxScreenshotsManager.prototype.init = function(maxScreenshots) {
    this.maxScreenshots = maxScreenshots ? maxScreenshots : 5;
    this.container = $('screenshotsContainer');
    this.fileAttacherContainer = $('screenshotsFileAttacherContainer');
    this.idsInput = $('screenshots_input');
    this.attachs = [];
    this.appendChild(
        this.fileAttacherContainer,
        this.fileAttacher = New(FileAttacher, [ 'screenshots', /^(jpg|png|jpeg|gif)$/i, BeastxWPProjectTexts.onlyImageFiles, 'image' ], { onuploadstart: this.caller('onUploadStart'), onuploaddone: this.caller('onUploadDone') })
    );
}

BeastxScreenshotsManager.prototype.onUploadStart = function() {
    this.fileAttacher.setEnabled(false);
}

BeastxScreenshotsManager.prototype.checkMaxLength = function() {
    if (this.maxScreenshots && (this.attachs.length >= this.maxScreenshots)) {
        this.fileAttacher.setEnabled(false);
        this.fileAttacher.updateStatusText(BeastxWPProjectTexts.fileMaxLength);
    } else {
        this.fileAttacher.setEnabled(true);
        this.fileAttacher.updateStatusText('');
    }
}

BeastxScreenshotsManager.prototype.onUploadDone = function(params) {
    this.fileAttacher.setEnabled(true);
    if (params.error) {
        alert(BeastxWPProjectTexts[params.errorMsg]);
    } else {
        this.addScreenshotItem(params.id, '', params.url);
    }
}

BeastxScreenshotsManager.prototype.addScreenshotItem = function(id, title, url) {
    var item = New(BeastxScreenshotItem, [id, title, url], { onremoveclick: this.caller('onRemoveItemClick'), onsetitle: this.caller('onSetTitle') });
    this.attachs.push(item);
    this.appendChild(this.container, item);
    this.updateIdsInputValue();
    this.checkMaxLength();
}

BeastxScreenshotsManager.prototype.onSetTitle = function(item) {
    this.updateIdsInputValue();
}

BeastxScreenshotsManager.prototype.onRemoveItemClick = function(item) {
    var newList = [];
    for (var i = 0; i < this.attachs.length; ++i) {
        if (this.attachs[i] != item) {
            newList.push(this.attachs[i]);
        }
    }
    this.removeChild(this.container, item);
    this.attachs = newList;
    this.updateIdsInputValue();
    this.checkMaxLength();
}

BeastxScreenshotsManager.prototype.triggerSave = function() {
    this.updateIdsInputValue();
}

BeastxScreenshotsManager.prototype.updateIdsInputValue = function() {
    this.idsInput.value = this.getValue();
}

BeastxScreenshotsManager.prototype.getValue = function() {
    var value = [];
    for (var i = 0; i < this.attachs.length; ++i) {
        value.push(this.attachs[i].getValue());
    }
    return VAR.serialize(value);
}









var BeastxScreenshotItem = function() {};
    
BeastxScreenshotItem.prototype.init = function(id, title, url) {
    this.randId = Math.random() * 100000000000000000;
    this.id = id;
    this.url = url;
    this.title = title;
    this.updateUI();
}

BeastxScreenshotItem.prototype.updateUI = function() {
    this.widget = this.element('div', { 'class': 'ScreenshotItem' }, [
        this.imgContainer = this.element('div', null, [
            this.imgElement = this.element('img', { width: '100', height: '100', src: this.url }, [])
        ]),
        this.element('a', { href: '#', onclick: this.caller('onRemoveClick') }, [ BeastxWPProjectTexts.remove ]),
        this.element('br'),
        this.setTitleLink = this.element('a', { href: '#', onclick: this.caller('onSetTitleClick') }, [ this.title != '' ? BeastxWPProjectTexts.editTitle : BeastxWPProjectTexts.setTitle ]),
        this.element('br'),
        this.editImageLink = this.element('a', { href: '#', onclick: this.caller('onEditClick') }, [ BeastxWPProjectTexts.editImage ])
    ]);
        
}

BeastxScreenshotItem.prototype.onEditClick = function(event) {
    DOM.cancelEvent(event);
    this.popup = this.element('div', { 'class': 'BeastxPopup' }, [
        this.element('div', { 'class': 'BeastxPopupTitle' }, [ BeastxWPProjectTexts.setTitleLong ]),
        this.element('div', { 'class': 'BeastxPopupContent' }, [
            this.editImageIframe = this.element('iframe', { id: 'editImageIframe_' + this.id, onload: this.caller('onEditImageIframeLoad'), width: '700px', height: '600px', src: '/wp-admin/media.php?attachment_id=' + this.id + '&action=edit', 'class': 'BeastxWPProjectEditorSimple' }),
            this.element('div', null, [
                this.element('button', { onclick: this.caller('closeEditImageIframe') }, [ BeastxWPProjectTexts.done ])
            ])
        ])
    ]);
    
    document.body.appendChild(this.popup);
    jQuery(this.popup).center();
}

BeastxScreenshotItem.prototype.onEditImageIframeLoad = function(event) {
    var me = this;
    var doc = this.editImageIframe.contentDocument ? this.editImageIframe.contentDocument : window.frames[this.editImageIframe.id].document;
    var form = doc.getElementById('media-single-form');
    doc.body.innerHTML = '';
    doc.body.appendChild(form);
    var button = doc.getElementById('imgedit-open-btn-' + this.id);
    button.click();
    setTimeout(function() {
        var submitButton = jQuery('.imgedit-submit-btn', doc)[0];
        DOM.addListener(submitButton, 'click', function() {
            setTimeout(function() {
                alert(1)
                form.submit();
                setTimeout(function() {
                    alert(2)
                    me.closeEditImageIframe();
                    setTimeout(function() {
                        me.reloadImage();
                    }, 1000);
                }, 1000);
            }, 1000);
        })
    }, 1000);
}

BeastxScreenshotItem.prototype.reloadImage = function() {
    this.imgElement = this.element('img', { width: '100', height: '100', src: this.url });
    this.replaceContent(this.imgContainer, this.imgElement);
    alert(this.url);
}

BeastxScreenshotItem.prototype.closeEditImageIframe = function() {
    document.body.removeChild(this.popup);
}

BeastxScreenshotItem.prototype.onSetTitleClick = function(event) {
    DOM.cancelEvent(event);
    this.popup = this.element('div', { 'class': 'BeastxPopup' }, [
        this.element('div', { 'class': 'BeastxPopupTitle' }, [ BeastxWPProjectTexts.setTitleLong ]),
        this.element('div', { 'class': 'BeastxPopupContent' }, [
            this.titlePopupArea = this.element('textarea', { id: 'screenshotPopup_' + this.randId, 'class': 'BeastxWPProjectEditorSimple' }, [ this.title ]),
            this.element('button', { onclick: this.caller('onCloseSetTitlePopupClick') }, [ BeastxWPProjectTexts.done ])
        ])
    ]);
    
    document.body.appendChild(this.popup);
    jQuery(this.popup).center();
    tinyMCE.execCommand('mceAddControl', false, 'screenshotPopup_' + this.randId);
}

BeastxScreenshotItem.prototype.onCloseSetTitlePopupClick = function() {
    this.title = tinyMCE.editors['screenshotPopup_' + this.randId].getContent();
    document.body.removeChild(this.popup);
    tinyMCE.execCommand('mceRemoveControl', false, 'screenshotPopup_' + this.randId);
    this.setTitleLink.innerHTML = this.title != '' ? BeastxWPProjectTexts.editTitle : BeastxWPProjectTexts.setTitle;
    this.dispatchEvent('settitle', this);
}

BeastxScreenshotItem.prototype.onRemoveClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm(BeastxWPProjectTexts.areYouSure);
    if (areYouSure) {
        this.dispatchEvent('removeclick', this);
    }
}

BeastxScreenshotItem.prototype.getValue = function() {
    return { id: this.id, title: this.title };
}











var BeastxFaqItem = function() {};
    
BeastxFaqItem.prototype.init = function(question, answer) {
    this.id = Math.random() * 100000000000000000;
    this.question = question ? question : '';
    this.answer = answer ? answer : '';
    this.updateUI();
    this.setEditMode(!question && !answer);
}

BeastxFaqItem.prototype.isEmpty = function() {
    return !this.answer && !this.question;
}

BeastxFaqItem.prototype.updateUI = function() {
    this.widget = this.element('tbody', null, [
        this.element('tr', { 'class': 'top question' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.question ])
            ]),
            this.questionInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ]),
            this.actionsConteiner = this.element('td', { 'class': 'actions', rowspan: '2' }, this.getViewModeActionsLinks())
        ]),
        this.element('tr', { 'class': 'bottom answer' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.answer ])
            ]),
            this.answerInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ])
        ])
    ]);
}

BeastxFaqItem.prototype.onEditClick = function(event) {
    DOM.cancelEvent(event);
    this.setEditMode(true);
}

BeastxFaqItem.prototype.onSaveClick = function(event) {
    DOM.cancelEvent(event);
    this.save();
    this.setEditMode(false);
    this.dispatchEvent('save', this);
    if (this.isEmpty()) {
        this.dispatchEvent('remove', this);
    }
}

BeastxFaqItem.prototype.save = function() {
    if (this.editMode) {
        this.question = this.questionInput.value;
        this.answer = tinyMCE.editors['faq_' + this.id].getContent();
        this.removeRichEditor();
    }
}

BeastxFaqItem.prototype.getEditModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onSaveClick') }, [ BeastxWPProjectTexts.done ])
    ];
}

BeastxFaqItem.prototype.getViewModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onEditClick') }, [ BeastxWPProjectTexts.edit ]),
        this.element('a', { href: '#', onclick: this.caller('onRemoveClick') }, [ BeastxWPProjectTexts.remove ]),
        this.element('a', { href: '#', onclick: this.caller('onCopyClick') }, [ BeastxWPProjectTexts.copy ])
    ];
}

BeastxFaqItem.prototype.setEditMode = function(editMode) {
    this.editMode = editMode;
    if (editMode) {
        this.replaceContent(this.questionInputConteiner, this.questionInput = this.element('input', { value: this.question }));
        this.replaceContent(this.answerInputConteiner, this.answerInput = this.element('textarea', { id: 'faq_' + this.id, name: 'faq_' + this.id, 'class': 'BeastxWPProjectEditorSimple', rows: 5, cols: 10 }, [ this.answer ]) );
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getEditModeActionsLinks()));
        setTimeout(this.caller('addRichEditor'), 0);
    } else {
        var questionSpan = this.element('span', { 'class': 'inputContent'  });
        questionSpan.innerHTML = this.question;
        var answerSpan = this.element('span', { 'class': 'inputContent'  });
        answerSpan.innerHTML = this.answer;
        this.replaceContent(this.questionInputConteiner, questionSpan);
        this.replaceContent(this.answerInputConteiner, answerSpan);
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getViewModeActionsLinks()));
    }
}

BeastxFaqItem.prototype.addRichEditor = function() {
    tinyMCE.execCommand('mceAddControl', false, 'faq_' + this.id);
}

BeastxFaqItem.prototype.removeRichEditor = function() {
    tinyMCE.execCommand('mceRemoveControl', false, 'faq_' + this.id);
}

BeastxFaqItem.prototype.onCopyClick = function(event) {
    DOM.cancelEvent(event);
    this.dispatchEvent('copy', this);
}

BeastxFaqItem.prototype.onRemoveClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm(BeastxWPProjectTexts.areYouSure);
    if (areYouSure) {
        this.dispatchEvent('remove', this);
    }
}

BeastxFaqItem.prototype.getValue = function() {
    return { question: this.question, answer: this.answer };
}













var BeastxContributorsItem = function() {};
    
BeastxContributorsItem.prototype.init = function(name, url) {
    this.id = Math.random() * 100000000000000000;
    this.name = name ? name : '';
    this.url = url ? url : '';
    this.updateUI();
    this.setEditMode(!name && !url);
}

BeastxContributorsItem.prototype.isEmpty = function() {
    return !this.name && !this.url;
}

BeastxContributorsItem.prototype.updateUI = function() {
    this.widget = this.element('tbody', null, [
        this.element('tr', { 'class': 'top name' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.name ])
            ]),
            this.nameInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ]),
            this.actionsConteiner = this.element('td', { 'class': 'actions', rowspan: '2' }, this.getViewModeActionsLinks())
        ]),
        this.element('tr', { 'class': 'bottom url' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.url ])
            ]),
            this.urlInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ])
        ])
    ]);
}

BeastxContributorsItem.prototype.onEditClick = function(event) {
    DOM.cancelEvent(event);
    this.setEditMode(true);
}

BeastxContributorsItem.prototype.onSaveClick = function(event) {
    DOM.cancelEvent(event);
    this.save();
    this.setEditMode(false);
    if (this.isEmpty()) {
        this.dispatchEvent('remove', this);
    }
}

BeastxContributorsItem.prototype.save = function() {
    if (this.editMode) {
        this.name = this.nameInput.value;
        this.url = this.urlInput.value;
    }
}

BeastxContributorsItem.prototype.getEditModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onSaveClick') }, [ BeastxWPProjectTexts.done ])
    ];
}

BeastxContributorsItem.prototype.getViewModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onEditClick') }, [ BeastxWPProjectTexts.edit ]),
        this.element('a', { href: '#', onclick: this.caller('onRemoveClick') }, [ BeastxWPProjectTexts.remove ]),
        this.element('a', { href: '#', onclick: this.caller('onCopyClick') }, [ BeastxWPProjectTexts.copy ])
    ];
}

BeastxContributorsItem.prototype.setEditMode = function(editMode) {
    this.editMode = editMode;
    if (editMode) {
        this.replaceContent(this.nameInputConteiner, this.nameInput = this.element('input', { value: this.name }));
        this.replaceContent(this.urlInputConteiner, this.urlInput = this.element('input', { value: this.url }));
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getEditModeActionsLinks()));
    } else {
        var nameSpan = this.element('span', { 'class': 'inputContent'  });
        nameSpan.innerHTML = this.name;
        var urlSpan = this.element('span', { 'class': 'inputContent'  });
        urlSpan.innerHTML = this.url;
        this.replaceContent(this.nameInputConteiner, nameSpan);
        this.replaceContent(this.urlInputConteiner, this.element('a', { href: (this.url.indexOf('http://') != -1 ? this.url : 'http://' + this.url) }, [ urlSpan ]));
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getViewModeActionsLinks()));
    }
}

BeastxContributorsItem.prototype.onCopyClick = function(event) {
    DOM.cancelEvent(event);
    this.dispatchEvent('copy', this);
}

BeastxContributorsItem.prototype.onRemoveClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm(BeastxWPProjectTexts.areYouSure);
    if (areYouSure) {
        this.dispatchEvent('remove', this);
    }
}

BeastxContributorsItem.prototype.getValue = function() {
    return { name: this.name, url: this.url };
}


























var BeastxChangeLogItem = function() {};
    
BeastxChangeLogItem.prototype.init = function(version, changes) {
    this.id = Math.random() * 100000000000000000;
    this.version = version ? version : '';
    this.changes = changes ? changes : '';
    this.updateUI();
    this.setEditMode(!version && !changes);
}

BeastxChangeLogItem.prototype.isEmpty = function() {
    return !this.version && !this.changes;
}

BeastxChangeLogItem.prototype.updateUI = function() {
    this.widget = this.element('tbody', null, [
        this.element('tr', { 'class': 'top' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.version ])
            ]),
            this.versionInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ]),
            this.actionsConteiner = this.element('td', { 'class': 'actions', rowspan: '2' }, this.getViewModeActionsLinks())
        ]),
        this.element('tr', { 'class': 'bottom' }, [
            this.element('td', { 'class': 'label' }, [
                this.element('label', null, [ BeastxWPProjectTexts.changes ])
            ]),
            this.changesInputConteiner = this.element('td', { 'class': 'input' }, [ ' ' ])
        ])
    ]);
}

BeastxChangeLogItem.prototype.onEditClick = function(event) {
    DOM.cancelEvent(event);
    this.setEditMode(true);
}

BeastxChangeLogItem.prototype.onSaveClick = function(event) {
    DOM.cancelEvent(event);
    this.save();
    this.setEditMode(false);
    if (this.isEmpty()) {
        this.dispatchEvent('remove', this);
    }
}

BeastxChangeLogItem.prototype.save = function() {
    if (this.editMode) {
        this.version = this.versionInput.value;
        this.changes = tinyMCE.editors['changeLog_' + this.id].getContent();
        this.removeRichEditor();
    }
}

BeastxChangeLogItem.prototype.getEditModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onSaveClick') }, [ BeastxWPProjectTexts.done ])
    ];
}

BeastxChangeLogItem.prototype.getViewModeActionsLinks = function() {
    return [
        this.element('a', { href: '#', onclick: this.caller('onEditClick') }, [ BeastxWPProjectTexts.edit ]),
        this.element('a', { href: '#', onclick: this.caller('onRemoveClick') }, [ BeastxWPProjectTexts.remove ]),
        this.element('a', { href: '#', onclick: this.caller('onCopyClick') }, [ BeastxWPProjectTexts.copy ])
    ];
}

BeastxChangeLogItem.prototype.setEditMode = function(editMode) {
    this.editMode = editMode;
    if (editMode) {
        this.replaceContent(this.versionInputConteiner, this.versionInput = this.element('input', { value: this.version }));
        this.replaceContent(this.changesInputConteiner, this.changesInput = this.element('textarea', { id: 'changeLog_' + this.id, name: 'changeLog_' + this.id, 'class': 'BeastxWPProjectEditorSimple', rows: 5, cols: 10 }, [ this.changes ]) );
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getEditModeActionsLinks()));
        setTimeout(this.caller('addRichEditor'), 0);
    } else {
        var versionSpan = this.element('span', { 'class': 'inputContent'  });
        versionSpan.innerHTML = this.version;
        var changesSpan = this.element('span', { 'class': 'inputContent'  });
        changesSpan.innerHTML = this.changes;
        this.replaceContent(this.versionInputConteiner, versionSpan);
        this.replaceContent(this.changesInputConteiner, changesSpan);
        this.replaceContent(this.actionsConteiner, this.element('span', null, this.getViewModeActionsLinks()));
    }
}

BeastxChangeLogItem.prototype.addRichEditor = function() {
    tinyMCE.execCommand('mceAddControl', false, 'changeLog_' + this.id);
}

BeastxChangeLogItem.prototype.removeRichEditor = function() {
    tinyMCE.execCommand('mceRemoveControl', false, 'changeLog_' + this.id);
}

BeastxChangeLogItem.prototype.onCopyClick = function(event) {
    DOM.cancelEvent(event);
    this.dispatchEvent('copy', this);
}

BeastxChangeLogItem.prototype.onRemoveClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm(BeastxWPProjectTexts.areYouSure);
    if (areYouSure) {
        this.dispatchEvent('remove', this);
    }
}

BeastxChangeLogItem.prototype.getValue = function() {
    return { version: this.version, changes: this.changes };
}










function decoratr_get_images(){ alert('ver como implementar esto directo desde el plugin') }
function decoratr_got_images(){}
function decoratr_error(){}
function insert_image(){}
function insert_text(){}