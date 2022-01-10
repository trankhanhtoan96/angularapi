import {Component, OnInit} from '@angular/core';
import {Meta, Title} from "@angular/platform-browser";
import {Session} from "../../services/Session.service";
import {Model} from "../../services/Model.service";

@Component({
    selector: 'FEHome',
    template: `
        <div class="page-wrapper" style="background-color:#fff">
            <div class="page-body">
                <div class="container-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <FEB3></FEB3>
                        </div>
                        <div class="col-md-4">
                            <div class="row row-0 align-items-center m-1">
                                <div class="col">
                                    <FEB2></FEB2>
                                </div>
                            </div>
                            <div class="row row-0 align-items-center m-1">
                                <div class="col">
                                    <FEB2></FEB2>
                                </div>
                            </div>
                            <div class="row row-0 align-items-center m-1">
                                <div class="col">
                                    <FEB2></FEB2>
                                </div>
                            </div>
                            <div class="row row-0 align-items-center m-1">
                                <div class="col">
                                    <FEB2></FEB2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
})
export class FEHome implements OnInit {
    constructor(public title: Title, public meta: Meta, public session: Session, public model: Model) {
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
    }
}
