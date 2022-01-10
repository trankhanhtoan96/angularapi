import {NgModule} from '@angular/core';
import {FormsModule} from "@angular/forms";
import {AdministrationComponent} from "./components/Administration.component";
import {AdministrationSettingComponent} from "./components/AdministrationSetting.component";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {RouterModule} from "@angular/router";
import {NgSelect2Module} from "ng-select2";
import {CommonModule} from "@angular/common";

@NgModule({
    declarations: [
        AdministrationComponent,
        AdministrationSettingComponent
    ],
    exports: [
        AdministrationComponent,
        AdministrationSettingComponent
    ],
    imports: [
        FormsModule,
        GlobalComponentsModule,
        RouterModule,
        NgSelect2Module,
        CommonModule,
    ],
    providers: [
    ]
})
export class AdministrationModule {
}
