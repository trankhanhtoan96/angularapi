import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'UserEditComponent',
    template: `
        <EditViewComponent *ngIf="bean.id" [bean]="bean" [beanName]="beanName" [config]="config" [moduleName]="moduleName"></EditViewComponent>
    `
})
export class UserEditComponent {
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
            't_email',
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

    constructor(private router: ActivatedRoute) {
        this.router.params.subscribe(params => {
            this.bean.id = params.id;
        });
    }
}
