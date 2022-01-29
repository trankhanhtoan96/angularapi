import {Component, OnInit} from '@angular/core';
import {Backend} from "../../../services/Backend.service";
import {Model} from "../../../services/Model.service";

declare var $: any;

@Component({
    selector: 'AdminComponent',
    templateUrl: '../templates/Administration.html'
})
export class AdministrationComponent implements OnInit {
    public selectedTopic = '';
    public topicOptions = [{id: '', name: 'Tất cả'}];
    public startdate = new Date(new Date().getFullYear().toString()+'-'+(new Date().getMonth()+1).toString()+'-01');
    public enddate = new Date();

    constructor(private backend: Backend, private model: Model) {
    }

    ngOnInit(): void {
        this.model.list('BlogCategory', 1000, 0).subscribe(res => {
            res.list.map(item => {
                this.topicOptions.push({id: item.id, name: item.name});
            })
        })
    }

    public get displayTopic() {
        const interval = this.topicOptions.find(i => i.id == this.selectedTopic);
        // @ts-ignore
        return interval.name;
    }

    public selectTopic(value: string) {
        this.selectedTopic = value;
        $("#topic-select").removeClass('show').attr('aria-expanded', 'false');
        $("#topic-menu").removeClass('show');
    }
}
