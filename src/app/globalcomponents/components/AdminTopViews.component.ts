import {Component, Input, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";
import {listInterval} from "../../modules/administration/components/Administration.component";
import {Metadata} from "../../services/Metadata.service";

interface ITopRow {
    user: any;
    blogId: string;
    image: string;
    title: string;
    views: string;
    slug?: string;
}

@Component({
    selector: '[AdminTopViewsComponent]',
    templateUrl: '../templates/AdminTopViews.html'
})
export class AdminTopViewsComponent implements OnInit {

    public topList: ITopRow[] = [];
    private _interval: number = 0;
    private _topic: string = '';
    private _start: any = '';
    private _end: any = '';

    @Input() set interval(value: number) {
        // track when interval has been change from parent component
        this._interval = value;
        this.loadTopList();
    }

    @Input() set topic(value: string) {
        this._topic = value;
        this.loadTopList();
    }

    @Input() set startdate(value: any) {
        // track when interval has been change from parent component
        this._start = value;
        this.loadTopList();
    }

    @Input() set enddate(value: any) {
        // track when interval has been change from parent component
        this._end = value;
        this.loadTopList();
    }

    get interval(): number {
        return this._interval;
    }

    get topic(): string {
        return this._topic;
    }

    public totalViews = 0;
    public top = 10;

    constructor(public session: Session, public backend: Backend, public metadata: Metadata) {
    }

    ngOnInit(): void {

    }

    private loadTopList() {
        this.metadata.spinnerLoading().then(ref => {
            this.backend.getRequestNoAuth('views/top', {period: this.interval, top: this.top, topic: this.topic, start: this._start.toISOString().substr(0,10), end: this._end.toISOString().substr(0,10)}).subscribe(res => {
                //clear old list
                this.topList = [];
                for (let i = 0; i < res.result.length; i++) {
                    const row = res.result[i];
                    this.topList.push({
                        blogId: row.id,
                        image: row.image,
                        title: row.name,
                        views: row.views,
                        slug: row.slug,
                        user: row.user
                    });
                }
                ref.instance.self.destroy();
            });
        });
    }

    public get displayInterval() {
        const interval = listInterval.find(i => i.value == this.interval);
        // @ts-ignore
        return interval.display;
    }
}
