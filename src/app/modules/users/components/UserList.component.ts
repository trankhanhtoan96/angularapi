import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {Model} from "../../../services/Model.service";
import {ToastComponent} from "../../../globalcomponents/components/toast.component";
import {Metadata} from "../../../services/Metadata.service";

@Component({
    selector: 'UserListComponent',
    templateUrl: '../templates/list.html'
})
export class UserListComponent implements OnInit {
    beanName: string = 'User';
    moduleName: string = 'Users';
    beanList: any = [];
    config = [
        'user_name',
        'last_name',
        'first_name',
        'email1',
        'user_image',
        'status'
    ];


    constructor(
        private router: ActivatedRoute,
        private model: Model,
        private toast: ToastComponent,
        private route: Router,
        private metadata: Metadata
    ) {

    }

    ngOnInit(): void {
        // if (this.bean.id != 'create') {
        //     this.model.get(this.moduleName, this.bean.id).subscribe(res => {
        //         this.bean = res;
        //     });
        // }
    }

    get routerModule(): string {
        return String(this.moduleName).toLowerCase();
    }

}
