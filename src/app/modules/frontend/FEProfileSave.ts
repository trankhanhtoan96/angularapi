import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Backend} from "../../services/Backend.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";
import {FEB6} from "../../globalcomponents/components/FEB6";

@Component({
    selector: 'FEProfileSave',
    template: `
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <h1 class="m-3 my-large-title">BÀI VIẾT ĐÃ LƯU</h1>
                        <ng-container *ngFor="let item of beanList">
                            <div class="col-md-6">
                                <div class="row row-0 align-items-center m-1">
                                    <div class="col">
                                        <FEB2 [bean]="item"></FEB2>
                                    </div>
                                </div>
                            </div>
                        </ng-container>
                    </div>
                </div>
            </div>
        </div>
    `
})
export class FEProfileSave implements OnInit {
    public id: string;
    public beanList: any;

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
        if (typeof sessionStorage != "undefined") {
            this.router.params.subscribe(params => {
                this.metadata.spinnerLoading().then(ref => {
                    this.backend.getRequestNoAuth('frontend/blogsaved/' + params['id']).subscribe(res => {
                        console.log(res);
                        this.beanList = res;
                        ref.instance.self.destroy();
                    });
                });
            });
        }
    }
}
