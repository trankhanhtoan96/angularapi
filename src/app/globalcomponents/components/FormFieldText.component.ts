import {Component, EventEmitter, OnInit, Output} from '@angular/core';

@Component({
    selector: 'FormFieldTextComponent',
    template: `
        <editor (ngModelChange)="onData.emit(content)" [(ngModel)]="content" [init]="config"></editor>
    `
})
export class FormFieldTextComponent implements OnInit {
    public config: any;
    public content: any;
    @Output() onData = new EventEmitter();

    constructor() {
    }

    ngOnInit(): void {
    }
}
