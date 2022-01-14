import {Component, EventEmitter, Input, Output} from '@angular/core';
import {Utils} from "../../services/Utils.service";

@Component({
    selector: 'FormFieldSlugComponent',
    template: `
        <input [(ngModel)]="content" (change)="onData.emit(utils.convertToSlug(content))" type="text" class="form-control">
    `
})
export class FormFieldSlugComponent {
    @Input() content;
    @Output() onData = new EventEmitter();

    constructor(public utils: Utils) {
    }
}
