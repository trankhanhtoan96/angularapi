import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'AdsTopComponent',
    template: `
        <div *ngIf="showAds" class="container-sm">
            <div class="row m-3">
                <div class="col text-center">
                    <a *ngIf="session.setting.ads_toplink; else noLink" [href]="session.setting.ads_toplink" target="_blank">
                        <img *ngIf="showAdsType=='image'" [src]="session.setting.ads_topimage" style="width:100%;max-width: 900px" alt="">
                    </a>
                    <ng-template #noLink>
                        <img *ngIf="showAdsType=='image'" [src]="session.setting.ads_topimage" style="width:100%;max-width: 900px" alt="">
                    </ng-template>
                    <div *ngIf="showAdsType=='code'" [innerHTML]="session.setting.ads_topgg"></div>
                </div>
            </div>
        </div>`
})
export class AdsTopComponent implements OnInit {

    constructor(public session: Session) {
    }

    ngOnInit(): void {
    }

    get showAdsType(): 'image' | 'code' | 'none' {
        if (new Date(this.session.setting.ads_toptime) > new Date() && this.session.setting.ads_topimage) {
            return 'image';
        }
        if (this.session.setting.ads_topgg) return 'code';
        return 'none';
    }

    get showAds(): boolean {
        return this.showAdsType != 'none';
    }
}
