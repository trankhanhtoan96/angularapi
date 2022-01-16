import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';

@Component({
    selector: 'FormFieldDateTimeComponent',
    template: `
        <div class="row g-2">
            <div class="col-2">
                <input [(ngModel)]="time" type="text" placeholder="__:__" class="form-control">
            </div>
            <div class="col-3">
                <select [(ngModel)]="day" class="form-select" (change)="sendData()">
                    <option value="">Ngày</option>
                    <option *ngFor="let i of dayOptions" [value]="i">{{i}}</option>
                </select>
            </div>
            <div class="col-3">
                <select [(ngModel)]="month" class="form-select" (change)="sendData()">
                    <option value="">Tháng</option>
                    <option *ngFor="let i of monthOptions" [value]="i">{{i}}</option>
                </select>
            </div>
            <div class="col-4">
                <select [(ngModel)]="year" class="form-select" (change)="sendData()">
                    <option value="">Năm</option>
                    <option *ngFor="let i of yearOptions" [value]="i">{{i}}</option>
                </select>
            </div>
        </div>
    `
})
export class FormFieldDateTimeComponent implements OnInit {
    @Input() data: any;
    @Output() onData = new EventEmitter();
    year: number;
    month: number;
    day: number;
    time: string;
    yearOptions: any;
    monthOptions: any;
    dayOptions: any;

    constructor() {
    }

    ngOnInit(): void {
        if (this.data) {
            const tmp = String(this.data).split('-');
            this.day = parseInt(tmp[2]);
            this.month = parseInt(tmp[1]);
            this.year = parseInt(tmp[0]);
        }
        this.yearOptions = this.range(new Date().getFullYear() - 10, new Date().getFullYear() + 10);
        this.monthOptions = this.range(1, 12);
        this.dayOptions = this.range(1, 31);
    }

    range(start, end) {
        return Array.from({length: (end - start)}, (v, k) => k + start);
    }

    sendData() {
        if (this.year && this.month && this.day && this.time) {
            this.onData.emit(this.year + '-' + this.month + '-' + this.day + ' ' + this.time);
        }
    }
}
