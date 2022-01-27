import {Component, Input} from '@angular/core';
import {Utils} from "../../services/Utils.service";
import {Session} from "../../services/Session.service";

@Component({
    selector: 'FEB3',
    template: `
        <div *ngIf="bean" class="post-feature d-none d-md-block">
            <img class="d-block w-100" [src]="bean.image" alt="" style="width: auto;height: 470px;object-fit: cover">
            <div class="post-feature-content">
                <h2 class="card-title m-0">
                    <a class="style-title m-0" style="color: #f1f1f1" [routerLink]="'/blog/'+bean.slug">
                        {{bean.name}}
                    </a>
                </h2>
                <p class="style-description mb-0" style="color: #f1f1f1">{{bean.description}}</p>
                <p class="style-name-author mt-2" style="font-size: 13px;color:#f1f1f1;font-weight: normal">
                    <i class="ti ti-user-check"></i><a style="color:#f1f1f1" href="/writer/6">{{bean.created_by_name}}</a> · <i class="ti ti-calendar-event"></i> {{bean.date_entered|date:'dd/MM/yyyy'}} · <i class="ti ti-folder"></i>
                    <a [routerLink]="'/categoryid/'+bean.category_id" [class]="'badge bg-'+utils.getBlogCategoryColor(bean.category_name,session.enum.blogcategory_color_style_options)+'-lt'">{{bean.category_name}}</a>
                </p>
            </div>
        </div>
        <div *ngIf="bean" class="card no-border m-1 d-block d-md-none">
            <div class="card-img-top img-responsive img-responsive-16x9" [style]="{'background-image':'url('+bean.image+')','border-radius':'7px'}"></div>
            <div class="card-body">
                <h2 class="card-title m-0">
                    <a class="style-title m-0" [routerLink]="'/blog/'+bean.slug">
                        {{bean.name}}
                    </a>
                </h2>
                <p class="style-description mb-0">{{bean.description}}</p>
                <p class="style-name-author mt-2" style="font-size: 13px;color:#0d0f39;font-weight: normal">
                    <i class="ti ti-user-check"></i><a style="color:#0d0f39" href="/writer/6">{{bean.created_by_name}}</a> · <i class="ti ti-calendar-event"></i> {{bean.date_entered|date:'dd/MM/yyyy'}} · <i class="ti ti-folder"></i>
                    <a [routerLink]="'/categoryid/'+bean.category_id" [class]="'badge bg-'+utils.getBlogCategoryColor(bean.category_name,session.enum.blogcategory_color_style_options)+'-lt'">{{bean.category_name}}</a>
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
