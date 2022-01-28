import {Component} from '@angular/core';

@Component({
    selector: 'BlogSubcribeEmailListComponent',
    template: `
        <ListViewComponent [beanList]="beanList" [beanName]="beanName" [config]="config" [moduleName]="moduleName" [totalCount]="totalCount"></ListViewComponent>
    `
})
export class BlogSubcribeEmailListComponent {
    beanName: string = 'BlogSubcribeEmail';
    moduleName: string = 'BlogSubcribeEmail';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name: 'name',
            link: true
        },
        {
            name: 'date_entered'
        }
    ];
}
