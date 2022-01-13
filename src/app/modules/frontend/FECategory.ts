import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'FECategory',
    template: `
        <div class="page-wrapper" style="background-color:#fff">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <h1 *ngIf="bean" class="my-large-title">Chuyên mục: {{bean.name}}</h1>
                        <div class="col-md-8">
                            <FEB3 [bean]="data.recent[0]"></FEB3>
                        </div>
                        <div class="col-md-4">
                            <ng-container *ngFor="let item of recent4">
                                <div class="row row-0 align-items-center m-1">
                                    <div class="col">
                                        <FEB2 [bean]="item"></FEB2>
                                    </div>
                                </div>
                            </ng-container>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <FEBAdsMidComponent></FEBAdsMidComponent>
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <h1 class="m-3 my-large-title">GẦN ĐÂY</h1>
                            <FEB4 *ngIf="slug" [category]="slug" [beanList]="recent10"></FEB4>
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
export class FECategory implements OnInit {
    public slug: string;
    public bean: any;
    public data: any = {
        recent: [],
        editorPick: [],
        topViews: []
    };
    public recent4: any = [];
    public recent10: any = [];

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
                    this.slug = params.slug;
                    this.backend.getRequestNoAuth('frontend/category/' + this.slug).subscribe(res => {
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
                this.metadata.spinnerLoading().then(ref=>{
                    this.backend.getRequestNoAuth('frontend/categorydata/' + params.slug).subscribe(res => {
                        console.log(res);
                        this.data = res;
                        this.recent4 = [];
                        for (let i = 1; i < this.data.recent.length && i <= 4; i++) {
                            this.recent4.push(this.data.recent[i]);
                        }
                        this.recent10 = [];
                        for (let i = 5; i < this.data.recent.length && i <= 14; i++) {
                            this.recent10.push(this.data.recent[i]);
                        }

                        ref.instance.self.destroy();
                    });
                });
            });
        }
    }
}
