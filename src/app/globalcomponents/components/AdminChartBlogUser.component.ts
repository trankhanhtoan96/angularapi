import {Component, Input, OnInit, ViewChild} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";
import {listInterval} from "../../modules/administration/components/Administration.component";
import {ChartConfiguration, ChartData, ChartEvent, ChartType} from 'chart.js';
import {BaseChartDirective} from 'ng2-charts';
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: '[AdminChartBlogUserComponent]',
    templateUrl: '../templates/AdminChartBlogUser.html'
})
export class AdminChartBlogUserComponent implements OnInit {

    public loaded: boolean = false;
    private _interval: number;
    private _topic: string;
    private _start: any;
    private _end: any;

    @Input() set interval(value: number) {
        // track when interval has been change from parent component
        this._interval = value;
        this.reloadChart();
    }

    @Input() set topic(value: string) {
        // track when interval has been change from parent component
        this._topic = value;
        this.reloadChart();
    }

    @Input() set startdate(value: any) {
        // track when interval has been change from parent component
        this._start = value;
        this.reloadChart();
    }

    @Input() set enddate(value: any) {
        // track when interval has been change from parent component
        this._end = value;
        this.reloadChart();
    }

    get interval(): number {
        return this._interval;
    }

    get topic(): string {
        return this._topic;
    }

    @ViewChild(BaseChartDirective) chart: BaseChartDirective | undefined;

    public barChartOptions: ChartConfiguration['options'] = {
        responsive: true,
        // We use these empty structures as placeholders for dynamic theming.
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    font: {
                        size: 10
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            },
        },
        elements: {
            bar: {
                backgroundColor: '#ffe5b0',
                hoverBackgroundColor: '#f8d674',
            }
        },
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },

    };
    public barChartType: any = 'bar';

    public barChartData: ChartData<'bar'> = {
        labels: [],
        datasets: [
            {data: [], label: 'Bài viết'},
        ]
    };


    constructor(public session: Session, public backend: Backend, public metadata: Metadata) {
    }

    ngOnInit(): void {
    }

    private reloadChart() {
        this.metadata.spinnerLoading().then(ref => {
            this.backend.getRequestNoAuth('blogs/groupbyuser', {period: this.interval, topic: this.topic, start: this._start.toISOString().substr(0,10), end: this._end.toISOString().substr(0,10)}).subscribe(res => {
                let gotData = [];
                let gotLabels = [];
                for (let i = 0; i < res.result.length; i++) {
                    const item = res.result[i];
                    // @ts-ignore
                    gotData.push(parseInt(item.num_of_blogs));
                    // @ts-ignore
                    gotLabels.push(item.last_name + ' ' + item.first_name);

                }
                console.log(gotData);
                this.barChartData.datasets[0].data = gotData;
                this.barChartData.labels = gotLabels;
                this.chart?.ngOnChanges({}); //re-render chart
                this.loaded = true;
                ref.instance.self.destroy();
            });
        });
    }

    public get displayInterval() {
        const interval = listInterval.find(i => i.value == this.interval);
        // @ts-ignore
        return interval.display;
    }

    // events
    public chartClicked({event, active}: { event?: ChartEvent, active?: {}[] }): void {
        console.log(event, active);
    }

    public chartHovered({event, active}: { event?: ChartEvent, active?: {}[] }): void {
        console.log(event, active);
    }

}
