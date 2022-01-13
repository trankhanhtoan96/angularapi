import {Component, Input} from '@angular/core';
import {Session} from "../../services/Session.service";
import {ToastComponent} from "./toast.component";
import {Metadata} from "../../services/Metadata.service";
import {Backend} from "../../services/Backend.service";

@Component({
    selector: 'FEBAuthor',
    template: `
        <div class="card mb-3">
            <div class="row row-0">
                <div class="col-auto">
                    <span class="avatar avatar-lg" [style]="{'background-image': 'url('+bean.user_image+')'}"></span>
                </div>
                <div class="col">
                    <div class="card-body" style="padding: 0.5rem; color: rgb(13, 15, 57);">
                        <div class="style-name-author"><a [routerLink]="'/profile/'+bean.id" style="color: rgb(13, 15, 57);"><span style="font-size: 15px;">{{bean.name}}</span></a></div>
                        <br>
                        <div class="style-name-author">
                            <i class="ti ti-calendar"></i>
                            {{blog.date_entered|date:"dd/MM/yyyy"}}
                        </div>
                        <br>
                        <div class="style-name-author">
                            <i class="ti ti-heart"></i>
                            {{blog.view_count}} lượt xem
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <button *ngIf="session.authData.id" (click)="save()" class="btn btn-primary btn-sm"><i class="ti ti-clock"></i> Lưu</button>
                </div>
            </div>
        </div>
    `
})
export class FEBAuthor {
    @Input() bean: any;
    @Input() blog: any;

    constructor(public session: Session, private toast: ToastComponent, private metadata: Metadata, private backend: Backend) {
    }

    save() {
        this.metadata.spinnerLoading().then(ref => {
            this.backend.postRequest('frontend/blogsaveprofile/' + this.blog.id + '/' + this.session.authData.userid).subscribe(res => {
                console.log(res);
                this.toast.success('Đã lưu');
                ref.instance.self.destroy();
            });
        });
    }
}
