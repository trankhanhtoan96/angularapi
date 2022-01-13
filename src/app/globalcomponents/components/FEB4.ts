import {Component, Input} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Utils} from "../../services/Utils.service";
import {Metadata} from "../../services/Metadata.service";
import {Backend} from "../../services/Backend.service";

@Component({
    selector: 'FEB4',
    template: `
        <ng-container *ngIf="beanList">
            <ng-container *ngFor="let bean of beanList">
                <div class="card no-border m-1">
                    <div class="row row-0">
                        <div class="col-12 col-md-4">
                            <img style="border-radius:4px" [src]="bean.image" class="w-100 h-100 object-contain cursor-pointer" alt="">
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="card-body">
                                <h2 class="card-title m-0">
                                    <a class="style-title" [routerLink]="'/blog/'+bean.slug">
                                        {{bean.name}}
                                    </a>
                                </h2>
                                <p *ngIf="bean.description" class="style-description">{{bean.description}}</p>
                                <p class="style-name-author mt-2" style="font-size: 13px;color:#0d0f39;font-weight: normal">
                                    <i class="ti ti-user-check"></i>&nbsp;<a style="color:#0d0f39" [href]="'/profile/'+bean.created_by">{{bean.created_by_name}}</a>
                                    &nbsp;<i class="ti ti-calendar-event"></i>&nbsp;{{bean.date_entered|date:'dd/MM/yyyy'}}
                                    &nbsp;<i class="ti ti-folder"></i>&nbsp;<a [routerLink]="'/categoryid/'+bean.category_id" [class]="'badge bg-'+utils.getBlogCategoryColor(bean.category_name,session.enum.blogcategory_color_style_options)+'-lt'">{{bean.category_name}}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="m-3">
            </ng-container>
            <div class="text-center">
                <button (click)="loadMore()" type="button" class="btn btn-primary">Xem thêm bài viết</button>
            </div>
        </ng-container>
    `
})
export class FEB4 {
    @Input() beanList = [];
    @Input() category;

    constructor(public session: Session, public utils: Utils, private metadata: Metadata, private backend: Backend) {
    }

    loadMore() {
        this.metadata.spinnerLoading().then(ref => {
            const url = this.category ? 'frontend/categoryloadmore/' + this.category : 'frontend/home/loadmore';
            this.backend.getRequestNoAuth(url, {offset: this.beanList.length})
                .subscribe(res => {
                    ref.instance.self.destroy();
                    this.beanList = this.beanList.concat(res);
                });
        })
    }
}
