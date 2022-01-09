import {Component, OnInit} from '@angular/core';
import {ActivatedRoute, Router} from "@angular/router";
import {Model} from "../../../services/Model.service";
import {ToastComponent} from "../../../globalcomponents/components/toast.component";
import {Metadata} from "../../../services/Metadata.service";

@Component({
    selector: 'UserEditComponent',
    templateUrl: '../templates/edit.html'
})
export class UserEditComponent implements OnInit {
    beanName: string = 'User';
    moduleName: string = 'Users';
    bean: any = {
        id: '',
        name: ''
    };
    config = [
        [
            'user_name',
            'last_name',
            'first_name'
        ],
        [
            'email1',
            'user_image',
            'status'
        ],
        [
            {
                name: 'description',
                span: 12
            }
        ]
    ];


    constructor(
        private router: ActivatedRoute,
        private model: Model,
        private toast: ToastComponent,
        private route: Router,
        private metadata: Metadata
    ) {
        this.router.params.subscribe(params => {
            this.bean.id = params.id;
        });
    }

    ngOnInit(): void {
        if (this.bean.id != 'create') {
            this.model.get(this.moduleName, this.bean.id).subscribe(res => {
                this.bean = res;
            });
        }
    }

    get routerModule():string{
        return String(this.moduleName).toLowerCase();
    }

    saveData() {
        this.metadata.spinnerLoading().then(ref => {
            this.model.save(this.moduleName, this.bean).subscribe(res => {
                this.toast.success('Đã lưu thành công!');
                ref.instance.self.destroy();
                this.route.navigate(['/admin/' + this.routerModule]);

                console.log(res);
            });
        });
    }
}
