import {Component} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'FEBAdsSideBar',
    template: `
        <div *ngIf="showAds" class="my-sticky mt-3">
            <a *ngIf="session.setting.ads_sidelink; else noLink" [href]="session.setting.ads_sidelink" target="_blank">
                <img *ngIf="showAdsType=='image'" [src]="session.setting.ads_sideimage" class="img-responsive" alt="" style="max-height: 600px; padding-top: 0;">
            </a>
            <ng-template #noLink>
                <img *ngIf="showAdsType=='image'" [src]="session.setting.ads_sideimage" class="img-responsive" alt="" style="max-height: 600px; padding-top: 0;">
            </ng-template>
            <div *ngIf="showAdsType=='code'" [innerHTML]="session.setting.ads_sidegg"></div>
        </div>
    `
})
export class FEBAdsSideBar {
    constructor(public session: Session) {
    }
    get showAdsType(): 'image' | 'code' | 'none' {
        if (new Date(this.session.setting.ads_sidetime) > new Date() && this.session.setting.ads_sideimage) {
            return 'image';
        }
        if (this.session.setting.ads_sidegg) return 'code';
        return 'none';
    }

    get showAds(): boolean {
        return this.showAdsType != 'none';
    }
}
