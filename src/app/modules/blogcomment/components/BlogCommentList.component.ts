import {Component} from '@angular/core';

@Component({
    selector: 'BlogCommentListComponent',
    template: `
        <ListViewComponent [beanList]="beanList" [beanName]="beanName" [config]="config" [moduleName]="moduleName" [totalCount]="totalCount"></ListViewComponent>
    `
})
export class BlogCommentListComponent {
    beanName: string = 'BlogComment';
    moduleName: string = 'BlogComment';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name: 'description',
        },
        {
            name: 'blog_name',
        },
        {
            name: 'date_entered'
        }
    ];
}
