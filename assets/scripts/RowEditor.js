var BeastxRowEditor = function() { this.scriptName = 'BeastxRowEditor' }

BeastxRowEditor.prototype.init = function(id, form, container, addNewLink, rowClassRef) {
    this.id = id;
    this.form = form;
    this.container = container;
    this.addNewLink = addNewLink;
    this.rowClassRef = rowClassRef;
    DOM.addListener(this.addNewLink, 'click', this.caller('onAddNewLinkClick'));
    DOM.addListener(this.form, 'submit', this.caller('onFormSubmit'));
    this.appendChild(
        this.container, 
        this.valueElement = this.element('textarea', { name: this.id, id: this.id, type: 'text', rows: '15', cols: 50, style: { display: 'none' } })
    );
    this.appendChild(
        this.container, 
        this.table = this.element('table', { 'class': 'RowEditorTable', width: '95%', cellPadding: 0, cellSpacing: 0 })
    );
    this.rows = [];
}

BeastxRowEditor.prototype.onFormSubmit = function(event) {
    this.valueElement.value = this.getValue(true);
}

BeastxRowEditor.prototype.onAddNewLinkClick = function(event) {
    DOM.cancelEvent(event);
    this.addRow(New(this.rowClassRef));
}

BeastxRowEditor.prototype.onRemove = function(rowObject) {
    this.removeRow(rowObject);
}

BeastxRowEditor.prototype.onCopy = function(rowObject) {
    //~ this.removeRow(rowObject);
}

BeastxRowEditor.prototype.addRow = function(rowObject) {
    this.rows.push(rowObject);
    rowObject.addListener('onremove', this.caller('onRemove'));
    rowObject.addListener('oncopy', this.caller('onCopy'));
    rowObject.addListener('onsave', this.caller('onItemSave'));
    this.appendChild(this.table, rowObject);
    this.valueElement.value = this.getValue();
}

BeastxRowEditor.prototype.onItemSave = function() {
    this.valueElement.value = this.getValue(true);
}

BeastxRowEditor.prototype.removeRow = function(rowObject) {
    var tempRows = [];
    for (var i = 0; i < this.rows.length; ++i) {
        if (this.rows[i] == rowObject) {
            this.removeChild(this.table, this.rows[i]);
        } else {
            tempRows.push(this.rows[i]);
        }
    }
    this.rows = tempRows;
}

BeastxRowEditor.prototype.getValue = function(forceSave) {
    var value = [];
    for (var i = 0; i < this.rows.length; ++i) {
        if (forceSave && this.rows[i].save) {
            this.rows[i].save();
        }
        if (!this.rows[i].isEmpty()) {
            value.push(this.rows[i].getValue());
        }
    }
    return VAR.serialize(value);
}








var BeastxCategoryRowEditor = function() { this.scriptName = 'BeastxCategoryRowEditor' }

BeastxCategoryRowEditor.prototype.init = function(categoryId, categorySlug, categoryName, enabled) {
    this.categoryId = categoryId ? categoryId : null;
    this.categorySlug = categorySlug ? categorySlug : '';
    this.categoryName = categoryName ? categoryName : '';
    this.enabled = typeof enabled != 'undefined' ? enabled : true;
    this.updateUI();
}

BeastxCategoryRowEditor.prototype.setValue = function(categoryId, categorySlug, categoryName, enabled) {
    this.categoryId = categoryId;
    this.categorySlug = categorySlug;
    this.categoryName = categoryName;
    this.enabled = enabled;
}

BeastxCategoryRowEditor.prototype.isEmpty = function() {
    return this.categorySlugInput.value == '' && this.categoryNameInput.value == '';
}

BeastxCategoryRowEditor.prototype.getValue = function() {
    return {
        id: this.categoryId,
        categorySlug: this.categorySlugInput.value,
        categoryName: this.categoryNameInput.value,
        enabled: this.enabledInput.checked ? 1 : 0
    }
}

BeastxCategoryRowEditor.prototype.getId = function() {
    return this.categoryId;
}

BeastxCategoryRowEditor.prototype.onRemoveLinkClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm('Are you sure?');
    if (areYouSure) {
        this.dispatchEvent('remove', this);
    }
}

BeastxCategoryRowEditor.prototype.updateUI = function() {
    this.widget = this.element('tbody', {}, [
        this.element('tr', null, [
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Slug: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.categorySlugInput = this.element('input', { type: 'text', value: this.categorySlug, size: '15' })
            ]),
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Name: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.categoryNameInput = this.element('input', { type: 'text', value: this.categoryName, size: '15' })
            ]),
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Enabled: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.enabledInput = this.element('input', { type: 'checkbox' })
            ]),
            this.element('td', { 'class': 'actionsTD' }, [
                this.removeLink = this.element('a', { href: '#', onclick: this.caller('onRemoveLinkClick'), title: 'Remove this category' }, [ 'Remove' ])
            ])
        ])
    ]);
    this.enabledInput.checked = this.enabled;
}














var BeastxLicenceRowEditor = function() { this.scriptName = 'BeastxLicenceRowEditor' }

BeastxLicenceRowEditor.prototype.init = function(id, name, url, enabled) {
    this.id = id ? id : null;
    this.name = name ? name : '';
    this.url = url ? url : '';
    this.enabled = typeof enabled != 'undefined' ? enabled : true;
    this.updateUI();
}

BeastxLicenceRowEditor.prototype.setValue = function(id, name, url, enabled) {
    this.id = id;
    this.name = name;
    this.url = url;
    this.enabled = enabled;
}

BeastxLicenceRowEditor.prototype.isEmpty = function() {
    return this.nameInput.value == '' && this.urlInput.value == '';
}

BeastxLicenceRowEditor.prototype.getValue = function() {
    return {
        id: this.categoryId,
        licenceName: this.nameInput.value,
        licenceUrl: this.urlInput.value,
        enabled: this.enabledInput.checked ? 1 : 0
    }
}

BeastxLicenceRowEditor.prototype.getId = function() {
    return this.id;
}

BeastxLicenceRowEditor.prototype.onRemoveLinkClick = function(event) {
    DOM.cancelEvent(event);
    var areYouSure = confirm('Are you sure?');
    if (areYouSure) {
        this.dispatchEvent('remove', this);
    }
}

BeastxLicenceRowEditor.prototype.updateUI = function() {
    this.widget = this.element('tbody', {}, [
        this.element('tr', null, [
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Name: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.nameInput = this.element('input', { type: 'text', value: this.name, size: '15' })
            ]),
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Url: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.urlInput = this.element('input', { type: 'text', value: this.url, size: '15' })
            ]),
            this.element('td', { 'class': 'labelTD' }, [
                this.element('label', null, [ 'Enabled: ' ])
            ]),
            this.element('td', { 'class': 'inputTD' }, [
                this.enabledInput = this.element('input', { type: 'checkbox' })
            ]),
            this.element('td', { 'class': 'actionsTD' }, [
                this.removeLink = this.element('a', { href: '#', onclick: this.caller('onRemoveLinkClick'), title: 'Remove this licence' }, [ 'Remove' ])
            ])
        ])
    ]);
    this.enabledInput.checked = this.enabled;
}
