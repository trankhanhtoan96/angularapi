import {Component, Input} from '@angular/core';
import {Utils} from "../../services/Utils.service";
import {Session} from "../../services/Session.service";

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
                    <a class="cursor-pointer" style="color:#0d0f39;font-size: 13px" [routerLink]="'/profile/'+bean.created_by">
                        <i class="ti ti-user-check"></i> {{bean.created_by_name}}
                    </a>
                    <br/>
                    <a style="color:#000;font-size: 13px" [routerLink]="'/category/'+bean.category_id">
                        <i class="ti ti-folder"></i>
                        <a [routerLink]="'/category/'+bean.category_id" [class]="'cursor-pointer badge bg-'+utils.getBlogCategoryColor(bean.category_name,session.enum.blogcategory_color_style_options)+'-lt'">{{bean.category_name}}</a>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    `
})
export class FEB2 {
    @Input() bean: any;

    constructor(public utils: Utils, public session: Session) {
    }
}
