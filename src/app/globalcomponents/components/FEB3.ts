import {Component, Input} from '@angular/core';
import {Utils} from "../../services/Utils.service";
import {Session} from "../../services/Session.service";

@Component({
    selector: 'FEB3',
    template: `
        <div *ngIf="bean" class="post-feature">
            <img class="d-block w-100" [src]="bean.image" alt="">
            <div class="post-feature-content">
                <h2 class="card-title m-0">
                    <a class="style-title m-0" style="color: #f1f1f1" [routerLink]="'/blog/'+bean.slug">
                        {{bean.name}}
                    </a>
                </h2>
                <p class="style-description mb-0" style="color: #f1f1f1">{{bean.description}}</p>
                <p class="style-name-author mt-2" style="font-size: 13px;color:#f1f1f1;font-weight: normal">
                    <i class="ti ti-user-check"></i><a style="color:#f1f1f1" href="/writer/6">{{bean.created_by_name}}</a> · <i class="ti ti-calendar-event"></i> {{bean.date_entered|date:'dd/MM/yyyy'}} · <i class="ti ti-folder"></i>
                    <a [routerLink]="'/category/'+bean.category_id" [class]="'badge bg-'+utils.getBlogCategoryColor(bean.category_name,session.enum.blogcategory_color_style_options)+'-lt'">{{bean.category_name}}</a>
                </p>
            </div>
        </div>
    `
})
export class FEB3 {
    @Input() bean: any;

    constructor(public utils: Utils, public session: Session) {
    }
}
