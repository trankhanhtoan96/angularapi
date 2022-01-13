import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'FEBAdsMidComponent',
    template: `
        <div *ngIf="showAds" class="container">
            <div class="row m-3">
                <div class="col text-center">
                    <img *ngIf="showAdsType=='image'" [src]="session.setting.ads_midimage" style="width:100%;max-width: 900px" alt="">
                    <div *ngIf="showAdsType=='code'" [innerHTML]="session.setting.ads_midgg"></div>
                </div>
            </div>
        </div>`
})
export class FEBAdsMidComponent implements OnInit {

    constructor(public session: Session) {
    }

    ngOnInit(): void {
    }

    get showAdsType(): 'image' | 'code' | 'none' {
        if (new Date(this.session.setting.ads_midtime) > new Date() && this.session.setting.ads_midimage) {
            return 'image';
        }
        if (this.session.setting.ads_midgg) return 'code';
        return 'none';
    }

    get showAds(): boolean {
        return this.showAdsType != 'none';
    }
}
