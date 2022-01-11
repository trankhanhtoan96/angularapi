import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'FEBlog',
    template: `
        <div class="page-wrapper" style="background-color:#fff">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <div *ngIf="bean" class="col-md-8">
                            <h1 style="font-size:30px;margin-top:1rem;margin-bottom: 3rem;">{{bean.name}}</h1>
                            <FEBAuthor [bean]="author" [blog]="bean"></FEBAuthor>
                            <div class="style-articleDetail p-3" [innerHTML]="bean.content"></div>
                            <FEBAdsMidComponent></FEBAdsMidComponent>
                        </div>
                        <div class="col-md-4">
                            <h1 class="m-3 my-large-title">CÙNG CHỦ ĐỀ</h1>
                            <ng-container *ngFor="let item of data.recent">
                                <div class="row row-0 align-items-center m-1">
                                    <div class="col">
                                        <FEB2 [bean]="item"></FEB2>
                                    </div>
                                </div>
                            </ng-container>
                            <br/>
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
export class FEBlog implements OnInit {
    public slug: string;
    public bean: any;
    public author: any;
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
                    this.slug = params.slug;
                    this.backend.getRequest('frontend/blog/' + this.slug).subscribe(res => {
                        console.log(res);
                        this.bean = res.bean;
                        this.author = res.author;
                        this.title.setTitle(this.bean.seo_title?this.bean.seo_title:this.bean.name);
                        this.meta.addTags([
                            {name: 'description', content: this.bean.seo_description?this.bean.seo_description:this.bean.description}
                        ]);
                    });
                });
            }
        });
        //no load on server
        if (typeof sessionStorage != "undefined") {
            this.router.params.subscribe(params => {
                this.metadata.spinnerLoading().then(ref=>{
                    this.backend.getRequest('frontend/blogdata/' + params.slug).subscribe(res => {
                        console.log(res);
                        this.data = res;
                        ref.instance.self.destroy();
                    });
                });
            });
        }
    }
}
