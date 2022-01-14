import {Component, Input, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Select2OptionData} from "ng-select2";
import {Model} from "../../services/Model.service";

@Component({
    selector: 'FormFormComponent',
    templateUrl: '../templates/FormForm.html'
})
export class FormFormComponent implements OnInit {
    @Input() beanName: string;
    @Input() moduleName: string;
    @Input() config: any;
    @Input() bean: any = null;
    loadedSystemInfo = false;
    fieldTypeAllow = [
        'varchar',
        'enum',
        'html',
        'image',
        'id',
        'relate',
        'text',
    ];
    optionsOfField = {};
    beingOptionsOfField = [];

    constructor(
        public session: Session,
        private model: Model) {
    }

    ngOnInit(): void {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) {
                this.loadedSystemInfo = true;
            }
        });
    }

    get fieldsDef(): [] {
        if (this.session.fields) return this.session.fields[this.beanName].fields;
        return [];
    }

    getFieldName(field: any): string {
        if (typeof field == 'string') return field;
        return field.name;
    }

    getFieldSpan(field: any): string {
        if (typeof field == 'string') return '4';
        if (field.hasOwnProperty('span')) return field.span;
        return '4';
    }

    getFieldRequired(field: any): string {
        // @ts-ignore
        let _field = this.fieldsDef[this.getFieldName(field)];
        if (_field.hasOwnProperty('required') && _field.required) return 'required';
        return '';
    }

    isFieldNoLabel(field: any): boolean {
        // @ts-ignore
        return !this.fieldsDef[this.getFieldName(field)].hasOwnProperty('vname');
    }

    getLabel(field: any): string {
        // @ts-ignore
        let _field = this.fieldsDef[this.getFieldName(field)];
        if (this.session.lang.hasOwnProperty(_field.vname)) {
            return this.session.lang[_field.vname];
        }
        return _field.vname;
    }

    getFieldType(field: any): string {
        // @ts-ignore
        let _type = this.fieldsDef[this.getFieldName(field)].type;
        if (this.fieldTypeAllow.indexOf(_type) < 0) {
            // @ts-ignore
            if (this.fieldsDef[this.getFieldName(field)].hasOwnProperty('dbType')) {
                // @ts-ignore
                _type = this.fieldsDef[this.getFieldName(field)].dbType;
                if (this.fieldTypeAllow.indexOf(_type) < 0) return '';
                return _type;
            }
            return '';
        }
        return _type;
    }

    getFieldLen(field: any): number {
        // @ts-ignore
        let _field = this.fieldsDef[this.getFieldName(field)];
        if (_field.hasOwnProperty('len')) {
            return _field.len;
        }
        return 255;
    }

    getFieldOptions(field: any): Select2OptionData[] {
        let result = [{id: '', text: ''}];
        // @ts-ignore
        let _field = this.fieldsDef[this.getFieldName(field)];
        let options = [];
        if (this.session.enum.hasOwnProperty(_field.options)) options = this.session.enum[_field.options];
        for (let key in options) {
            result.push({id: key, text: options[key]});
        }
        return result;
    }

    getFieldRelateOptions(field: any): Select2OptionData[] {
        let fieldName = this.getFieldName(field);
        if (this.optionsOfField.hasOwnProperty(fieldName)) { // @ts-ignore
            return this.optionsOfField[fieldName];
        }
        // @ts-ignore
        if (this.beingOptionsOfField.indexOf(fieldName) >= 0) return [];
        // @ts-ignore
        this.beingOptionsOfField.push(fieldName);
        for (let _field in this.fieldsDef) {
            // @ts-ignore
            if (this.getFieldType(_field) == 'relate' && this.fieldsDef[_field].id_name == fieldName) {
                // @ts-ignore
                this.model.list(this.fieldsDef[_field].module, 1000, 0).subscribe(res => {
                    // console.log(res);
                    let result = [{id: '', text: ''}];
                    for (let bean of res.list) {
                        result.push({id: bean.id, text: bean.name});
                    }
                    // @ts-ignore
                    this.optionsOfField[this.getFieldName(field)] = result;
                    // @ts-ignore
                    this.beingOptionsOfField.splice(this.beingOptionsOfField.indexOf(fieldName), 1);
                });
            }
        }
        return [{id: '', text: ''}];
    }

    enableFieldRelate(field: any): boolean {
        let fieldName = this.getFieldName(field);
        if (this.optionsOfField.hasOwnProperty(fieldName)) return true;
        this.getFieldRelateOptions(field);
        return false;
    }
}
