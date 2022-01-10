import {Component, Input, OnInit} from '@angular/core';
import {Model} from "../../services/Model.service";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'ListViewComponent',
    templateUrl: '../templates/ListView.html'
})
export class ListViewComponent implements OnInit {
    @Input() beanName: string = '';
    @Input() moduleName: string = '';
    @Input() beanList: any = [];
    @Input() totalCount: number = 0;
    @Input() config = [];


    constructor(
        private model: Model,
        private metadata: Metadata
    ) {

    }

    ngOnInit(): void {
        this.refreshBeanList();
    }

    get routerModule(): string {
        return String(this.moduleName).toLowerCase();
    }

    refreshBeanList() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.list(this.moduleName, 20, 0).subscribe(res => {console.log(res)
                this.beanList = res.list;
                this.totalCount = res.totalcount;
                ref.instance.self.destroy();
            });
        });
    }

    loadMore() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.list(this.moduleName, 20, this.beanList.length).subscribe(res => {
                this.beanList = this.beanList.contat(res.list);
                ref.instance.self.destroy();
            });
        });
    }
}