import {Component} from '@angular/core';

@Component({
    selector: 'BlogCategoryListComponent',
    template: `
        <ListViewComponent [beanList]="beanList" [beanName]="beanName" [config]="config" [moduleName]="moduleName" [totalCount]="totalCount"></ListViewComponent>
    `
})
export class BlogCategoryListComponent {
    beanName: string = 'BlogCategory';
    moduleName: string = 'BlogCategory';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name: 'name',
            link: true
        },
        {
            name:'color_style'
        },
        {
            name: 'parent_name'
        },
        {
            name: 'date_entered'
        }
    ];
}
