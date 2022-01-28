import {Component, Input, OnInit, ViewChild} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";
import {listInterval} from "../../modules/administration/components/Administration.component";
import {ChartConfiguration, ChartEvent, ChartType} from 'chart.js';
import {BaseChartDirective} from 'ng2-charts';
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: '[AdminChartViewsComponent]',
    templateUrl: '../templates/AdminChartViews.html'
})
export class AdminChartViewsComponent implements OnInit {

    public totalViews: number = 0;
    public loaded: boolean = false;
    private _interval: number;

    @Input() set interval(value: number) {
        // track when interval has been change from parent component
        this._interval = value;
        this.reloadChart();
    }

    get interval(): number {
        return this._interval;
    }

    public lineChartData: ChartConfiguration['data'] = {
        datasets: [
            {
                data: [],
                label: 'Lượt xem',
                backgroundColor: 'rgba(148,159,177,0.2)',
                borderColor: '#f8d674',
                pointBackgroundColor: '#f8d388',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(148,159,177,0.8)',
                fill: 'origin',
            }
        ],
        labels: []
    };

    public lineChartOptions: ChartConfiguration['options'] = {
        elements: {
            line: {
                tension: 0
            }
        },
        scales: {
            // We use this empty structure as a placeholder for dynamic theming.
            x: {},
            'y-axis-0':
                {
                    position: 'left',
                }
        },

        plugins: {
            legend: {display: false}
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    public lineChartType: ChartType = 'line';

    @ViewChild(BaseChartDirective) chart?: BaseChartDirective;


    constructor(public session: Session, public backend: Backend, public metadata: Metadata) {
    }

    ngOnInit(): void {
    }

    private reloadChart() {
        this.metadata.spinnerLoading().then(ref => {

            //clear total views
            this.totalViews = 0;

            this.backend.getRequestNoAuth('views/analyze', {period: this.interval}).subscribe(res => {
                let gotData = [];
                let gotLabels = [];
                for (let i = 0; i < res.result.length; i++) {
                    const item = res.result[i];
                    // @ts-ignore
                    gotData.push(item.views);
                    // @ts-ignore
                    gotLabels.push(item.date);

                    this.totalViews += parseInt(item.views);
                }
                this.lineChartData.datasets[0].data = gotData;
                this.lineChartData.labels = gotLabels;
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
