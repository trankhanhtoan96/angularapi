import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'BlogCommentEditComponent',
    template: `
        <EditViewComponent *ngIf="bean.id" [bean]="bean" [beanName]="beanName" [config]="config" [moduleName]="moduleName"></EditViewComponent>
    `
})
export class BlogCommentEditComponent {
    beanName: string = 'BlogComment';
    moduleName: string = 'BlogComment';
    bean: any = {
        id: '',
        name: ''
    };
    config = [
        [
            {
                name: 'name',
                span: 8
            },
            {
                name: 'blog_id'
            },
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
