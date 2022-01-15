import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Metadata} from "../../services/Metadata.service";
import {ToastComponent} from "./toast.component";

@Component({
    selector: 'TableListComponent',
    templateUrl: '../templates/TableList.html'
})
export class TableListComponent implements OnInit {
    @Input() beanName: string;
    @Input() moduleName: string;
    @Input() config: any;
    @Input() beanList: any = [];
    @Input() totalCount: number = 0;
    @Output() refreshBeanList = new EventEmitter();
    @Output() loadMore = new EventEmitter();
    loadedSystemInfo = false;
    fieldTypeAllow = [
        'varchar',
        'enum',
        'html',
        'image',
        'datetime',
        'text',
        'relate',
        'date'
    ];
    checked: string[] = [];

    constructor(
        public session: Session,
        private metadata: Metadata,
        private toast: ToastComponent,
        private model: Model) {
    }

    ngOnInit(): void {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) this.loadedSystemInfo = true;
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

    isChecked(id: string): boolean {
        return this.checked.indexOf(id) >= 0;
    }

    changeChecked(id: string) {
        const i = this.checked.indexOf(id);
        if (i >= 0) {
            this.checked.splice(i, 1);
        } else {
            this.checked.push(id);
        }
    }

    getLabel(field: any): string {
        let _field = this.fieldsDef[this.getFieldName(field)];
        if (this.session.lang.hasOwnProperty(_field.vname)) {
            return this.session.lang[_field.vname];
        }
        return _field.vname;
    }

    getFieldType(field: any): string {
        let _type = this.fieldsDef[this.getFieldName(field)].type;
        if (this.fieldTypeAllow.indexOf(_type) < 0) {
            if (this.fieldsDef[this.getFieldName(field)].hasOwnProperty('dbType')) {
                _type = this.fieldsDef[this.getFieldName(field)].dbType;
                if (this.fieldTypeAllow.indexOf(_type) < 0) return '';
                return _type;
            }
            return '';
        }
        return _type;
    }

    get routerModule(): string {
        return String(this.moduleName).toLowerCase();
    }

    checkall($event) {
        this.checked = [];
        if ($event.target.checked) {
            for (let bean of this.beanList) {
                this.checked.push(bean.id);
            }
        }
    }

    deleteBeansSelected() {
        this.metadata.spinnerLoading().then(ref => {
            for (const id of this.checked) {
                this.model.deletes(this.moduleName, this.checked).subscribe(res => {
                    if (res.success) {
                        this.toast.success('Đã xóa thành công!');
                    }
                    this.refreshBeanList.emit(true);
                    ref.instance.self.destroy();
                    // console.log(res);
                });
            }
        });
    }
}
