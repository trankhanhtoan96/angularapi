import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';

@Component({
    selector: 'FormFormComponent',
    templateUrl: '../templates/FormForm.html'
})
export class FormFormComponent implements OnInit {
    @Input() config: any;
    @Input() model: any;
    @Output() onData = new EventEmitter();

    constructor() {
    }

    ngOnInit(): void {
    }
}
