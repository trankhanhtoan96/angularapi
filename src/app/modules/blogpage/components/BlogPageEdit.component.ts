import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'BlogPageEditComponent',
    template: `
        <EditViewComponent *ngIf="bean.id" [bean]="bean" [beanName]="beanName" [config]="config" [moduleName]="moduleName"></EditViewComponent>
    `
})
export class BlogPageEditComponent {
    beanName: string = 'BlogPage';
    moduleName: string = 'BlogPage';
    bean: any = {
        id: '',
        name: ''
    };
    config = [
        [
            {
                name: 'name',
            },
            {
                name: 'slug'
            },
            {
                name: 'status'
            }
        ],
        [
            {
                name: 'seo_title'
            },
            {
                name: 'seo_keywork'
            },
            {
                name: 'seo_description'
            }
        ],
        [
            {
                name: 'description',
                span: 8
            },
            {
                name: 'image'
            }
        ],
        [
            {
                name: 'content',
                span: 12
            },
        ],
    ];

    constructor(private router: ActivatedRoute) {
        this.router.params.subscribe(params => {
            this.bean.id = params.id;
        });
    }
}
