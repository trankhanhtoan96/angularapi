import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";
import {FEB6} from "../../globalcomponents/components/FEB6";

@Component({
    selector: 'FEProfile',
    template: `
        <div *ngIf="bean" class="page-wrapper" style="background-color: rgb(255, 255, 255);">
            <div class="page-body">
                <div class="container-sm">
                    <div class="card" style="border: none; box-shadow: none;">
                        <div class="card-body p-4 text-center"><span class="avatar avatar-2xl mb-3 avatar-rounded" style="background-image: url({{bean.user_image}});"></span>
                            <h3 class="m-0 mb-1">
                                {{bean.name}}
                                <i *ngIf="session.authData.userid==id" class="ti ti-edit cursor-pointer" style="font-size: 20px" (click)="editProfile()"></i>
                            </h3>
                            <div class="text-muted">{{bean.t_email}}</div>
                            <div class="mt-3">
                                <span class="badge bg-danger">Writer</span>
                                <div class="style-articleDetail" [innerHTML]="bean.description"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 class="m-3 my-large-title">GẦN ĐÂY</h1>
                            <FEB4 *ngIf="id" [userid]="id" [beanList]="data.recent"></FEB4>
                        </div>
                        <div class="col-md-4">
                            <h1 class="m-3 my-large-title">XEM NHIỀU</h1>
                            <ng-container *ngFor="let item of data.topViews">
                                <div class="row row-0 align-items-center m-1">
                                    <div class="col">
                                        <FEB2 [bean]="item"></FEB2>
                                    </div>
                                </div>
                            </ng-container>
                            <FEBAdsSideBar></FEBAdsSideBar>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
})
export class FEProfile implements OnInit {
    public id: string;
    public bean: any;
    public data: any = {
        recent: [],
        topViews: []
    };

    constructor(
        public title: Title,
        public meta: Meta,
        public session: Session,
        public model: Model,
        public backend: Backend,
        private router: ActivatedRoute,
        private metadata: Metadata
    ) {
    }

    ngOnInit() {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) {
                this.router.params.subscribe(params => {
                    this.id = params['id'];
                    this.backend.getRequestNoAuth('frontend/profile/' + this.id).subscribe(res => {
                        console.log(res);
                        this.bean = res;
                        this.title.setTitle(this.bean.name + ' - ' + this.session.setting.system_name);
                        this.meta.addTags([
                            {name: 'description', content: this.session.setting.system_info}
                        ]);
                    });
                });
            }
        });
        //no load on server
        if (typeof sessionStorage != "undefined") {
            this.router.params.subscribe(params => {
                this.metadata.spinnerLoading().then(ref => {
                    this.backend.getRequestNoAuth('frontend/profiledata/' + params['id']).subscribe(res => {
                        console.log(res);
                        this.data = res;
                        ref.instance.self.destroy();
                    });
                });
            });
        }
    }

    editProfile() {
        this.metadata.modal(FEB6, 'large');
    }
}
