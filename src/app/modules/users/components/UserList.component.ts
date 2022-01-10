import {Component} from '@angular/core';

@Component({
    selector: 'UserListComponent',
    template: `
        <ListViewComponent [beanList]="beanList" [beanName]="beanName" [config]="config" [moduleName]="moduleName" [totalCount]="totalCount"></ListViewComponent>
    `
})
export class UserListComponent {
    beanName: string = 'User';
    moduleName: string = 'Users';
    beanList: any = [];
    totalCount: number = 0;
    config = [
        {
            name: 'user_image'
        },
        {
            name: 'user_name',
            link: true
        },
        {
            name: 'last_name'
        },
        {
            name: 'first_name'
        },
        {
            name: 'email1'
        },
        {
            name: 'status'
        },
        {
            name: 'date_entered'
        }
    ];
}