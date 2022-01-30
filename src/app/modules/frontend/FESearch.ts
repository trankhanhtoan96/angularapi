import {Component, OnInit} from '@angular/core';
import {Model} from "../../services/Model.service";
import {ActivatedRoute} from "@angular/router";
import {Metadata} from "../../services/Metadata.service";

@Component({
    selector: 'FESearch',
    template: `
        <div class="page-wrapper">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <h1 class="m-3 my-large-title">TÌM KIẾM BÀI VIẾT: {{term}}</h1>
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
export class FESearch implements OnInit {
    public term: string;
    public beanList: any;

    constructor(
        public model: Model,
        private router: ActivatedRoute,
        private metadata: Metadata
    ) {
    }

    ngOnInit() {
        if (typeof sessionStorage != "undefined") {
            this.router.params.subscribe(params => {
                this.term = params['term'];
                this.metadata.spinnerLoading().then(ref => {
                    this.model.list('Blog',20,0,this.term).subscribe(res => {
                        this.beanList = res.list;
                        ref.instance.self.destroy();
                    });
                });
            });
        }
    }
}
