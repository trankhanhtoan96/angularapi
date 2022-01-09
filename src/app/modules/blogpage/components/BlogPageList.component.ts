import {Component} from '@angular/core';

@Component({
    selector: 'BlogPageListComponent',
    template: `
        <ListViewComponent [beanList]="beanList" [beanName]="beanName" [config]="config" [moduleName]="moduleName" [totalCount]="totalCount"></ListViewComponent>
    `
})
export class BlogPageListComponent {
    beanName: string = 'BlogPage';
    moduleName: string = 'BlogPage';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name:'image'
        },
        {
            name: 'name',
            link: true
        },
        {
            name: 'description'
        },
        {
            name: 'status'
        },
        {
            name: 'date_entered'
        },
        {
            name: 'created_by_name'
        }
    ];
}
