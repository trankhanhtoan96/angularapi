import {Component, Input} from '@angular/core';

@Component({
    selector: 'FEB2',
    template: `
        <table *ngIf="bean">
            <tbody>
            <tr>
                <td>
                    <span class="avatar avatar-xl" [style]="{'background-image':'url('+bean.image+')'}"></span>
                </td>
                <td class="p-1">
                    <a class="style-titleArticle" [routerLink]="'/blog/'+bean.slug">
                        {{bean.name}}
                    </a>
                    <a style="color:#000;font-size: 13px" [routerLink]="'/profile/'+bean.created_by">
                        <i class="ti ti-user-check"></i> {{bean.created_by_name}}
                    </a>
                    <br/>
                    <a style="color:#000;font-size: 13px" [routerLink]="'/category/'+bean.category_id">
                        <i class="ti ti-folder"></i> {{bean.category_name}}
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    `
})
export class FEB2 {
    @Input() bean: any;

    constructor() {
    }
}
