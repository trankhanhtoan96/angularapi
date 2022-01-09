import {Component, OnInit} from '@angular/core';
import {Model} from "../../../services/Model.service";
import {Metadata} from "../../../services/Metadata.service";

@Component({
    selector: 'UserListComponent',
    templateUrl: '../templates/list.html'
})
export class UserListComponent implements OnInit {
    beanName: string = 'User';
    moduleName: string = 'Users';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name: 'user_image'
        },
        {
            name: 'user_name',
            link: true
        },
        {
            name: 'last_name'
        },
        {
            name: 'first_name'
        },
        {
            name: 'email1'
        },
        {
            name: 'status'
        },
        {
            name: 'date_entered'
        }
    ];


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
            this.model.list(this.moduleName, 20, 0).subscribe(res => {
                this.beanList = res.list;
                this.totalCount = res.totalcount;
                console.log(res);
                ref.instance.self.destroy();
            });
        });
    }

    loadMore() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.list(this.moduleName, 20, this.beanList.length).subscribe(res => {
                this.beanList = this.beanList.contat(res.list);
                console.log(res);
                ref.instance.self.destroy();
            });
        });
    }
}
