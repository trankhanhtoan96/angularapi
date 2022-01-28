import {Component, OnInit} from '@angular/core';
import {Backend} from "../../../services/Backend.service";
import {Model} from "../../../services/Model.service";

declare var $: any;

export interface IInterval {
    display: string;
    value: number; //day
}

export const listInterval: IInterval[] = [
    {display: "1 tuần qua", value: 7},
    {display: "30 ngày qua", value: 30},
    {display: "Tất cả", value: 0}
];

@Component({
    selector: 'AdminComponent',
    templateUrl: '../templates/Administration.html'
})
export class AdministrationComponent implements OnInit {

    public selectedInterval = 30;
    public selectedTopic = '';
    public intervalOptions = listInterval;
    public topicOptions = [{id: '', name: 'Tất cả'}];

    constructor(private backend: Backend, private model: Model) {
    }

    ngOnInit(): void {
        this.model.list('BlogCategory', 1000, 0).subscribe(res => {
            res.list.map(item => {
                this.topicOptions.push({id: item.id, name: item.name});
            })
        })
    }

    public get displayInterval() {
        const interval = listInterval.find(i => i.value == this.selectedInterval);
        // @ts-ignore
        return interval.display;
    }

    public get displayTopic() {
        const interval = this.topicOptions.find(i => i.id == this.selectedTopic);
        // @ts-ignore
        return interval.name;
    }

    public selectInterval(value: number) {
        this.selectedInterval = value;
        $("#interval-select").removeClass('show').attr('aria-expanded', 'false');
        $("#interval-menu").removeClass('show');
    }

    public selectTopic(value: string) {
        this.selectedTopic = value;
        $("#topic-select").removeClass('show').attr('aria-expanded', 'false');
        $("#topic-menu").removeClass('show');
    }
}
