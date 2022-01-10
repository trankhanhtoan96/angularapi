import {Component} from '@angular/core';
import {ActivatedRoute} from "@angular/router";

@Component({
    selector: 'BlogEditComponent',
    template: `
        <EditViewComponent *ngIf="bean.id" [bean]="bean" [beanName]="beanName" [config]="config" [moduleName]="moduleName"></EditViewComponent>
    `
})
export class BlogEditComponent {
    beanName: string = 'Blog';
    moduleName: string = 'Blog';
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
            },
            {
                name: 'allow_comment'
            },
            {
                name: 'category_id'
            }
        ],
        [
            {
                name: 'content',
                span: 8
            },
            {
                name: 'image'
            }
        ],
    ];

    constructor(private router: ActivatedRoute) {
        this.router.params.subscribe(params => {
            this.bean.id = params.id;
        });
    }
}
