import {Component, Input} from '@angular/core';

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
                <p class="style-description mb-0" style="color: #f1f1f1">Tối qua, Facebook đã thông báo đổi tên công ty thành Meta, nhằm phản ánh tham vọng tăng trưởng vượt ngoài mạng xã hội của công ty. Quyết định đổi tên này cho…</p>
                <p class="style-name-author mt-2" style="font-size: 13px;color:#f1f1f1;font-weight: normal">
                    <i class="ti ti-user-check"></i><a style="color:#f1f1f1" href="/writer/6">{{bean.created_by_name}}</a> · <i class="ti ti-calendar-event"></i> {{bean.date_entered}} · <i class="ti ti-folder"></i> {{bean.category_name}}
                </p>
            </div>
        </div>
    `
})
export class FEB3 {
    @Input() bean: any;

    constructor() {
    }
}
