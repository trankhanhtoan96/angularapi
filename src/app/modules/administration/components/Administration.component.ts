import {Component, OnInit} from '@angular/core';
declare var $: any;
export interface IInterval {
    display: string;
    value: number; //day
}

export const  listInterval: IInterval[] = [
    {display: "1 tuần qua", value: 7},
    {display: "30 ngày qua", value: 30},
    {display: "Tất cả", value: 0}
];

@Component({
    selector: 'AdminComponent',
    templateUrl: '../templates/Administration.html'
})
export class AdministrationComponent implements OnInit {

    public selectedInterval = 7;
    public intervalOptions = listInterval;

    constructor() {
    }

    ngOnInit(): void {
    }

    public get displayInterval(){
        const interval = listInterval.find(i=> i.value ==this.selectedInterval);
        // @ts-ignore
        return interval.display;
    }

    public selectInterval(value: number) {
        this.selectedInterval = value;
        $("#interval-select").removeClass('show').attr('aria-expanded', 'false');
        $("#interval-menu").removeClass('show');
    }
}
