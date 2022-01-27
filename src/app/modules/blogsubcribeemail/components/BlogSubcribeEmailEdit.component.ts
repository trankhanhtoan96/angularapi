import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'BlogSubcribeEmailEditComponent',
    template: `
        <EditViewComponent *ngIf="bean.id" [bean]="bean" [beanName]="beanName" [config]="config" [moduleName]="moduleName"></EditViewComponent>
    `
})
export class BlogSubcribeEmailEditComponent {
    beanName: string = 'BlogSubcribeEmail';
    moduleName: string = 'BlogSubcribeEmail';
    bean: any = {
        id: '',
        name: ''
    };
    config = [
        [
            {
                name: 'name',
                span: 12
            },
        ],
    ];

    constructor(private router: ActivatedRoute) {
        this.router.params.subscribe(params => {
            this.bean.id = params['id'];
        });
    }
}
