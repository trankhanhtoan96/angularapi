import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";
import {Backend} from "../../services/Backend.service";

@Component({
    selector: 'FEHome',
    template: `
        <div class="page-wrapper" style="background-color:#fff">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <FEB3 [bean]="data.recent[0]"></FEB3>
                        </div>
                        <div class="col-md-4">
                            <ng-container *ngFor="let bean of recent4">
                                <div  class="row row-0 align-items-center m-1">
                                    <div class="col">
                                        <FEB2 [bean]="bean"></FEB2>
                                    </div>
                                </div>
                            </ng-container>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
})
export class FEHome implements OnInit {
    public data: any = {
        recent: [],
        editorPick: [],
        topViews: []
    };
    public recent4:any=[];

    constructor(public title: Title, public meta: Meta, public session: Session, public model: Model, public backend: Backend) {
    }

    ngOnInit() {
        this.model.$loadedSystemInfo.subscribe(res => {
            if (res) {
                this.title.setTitle(this.session.setting.system_name);
                this.meta.addTags([
                    {name: 'description', content: this.session.setting.system_info}
                ]);
            }
        });
        this.backend.getRequest('frontend/home').subscribe(res => {
            console.log(res);
            this.data = res;
            this.recent4=[];
            for (let i = 1; i < this.data.recent.length && i <= 4; i++) {
                this.recent4.push(this.data.recent[i]);
            }
        });
    }
}
