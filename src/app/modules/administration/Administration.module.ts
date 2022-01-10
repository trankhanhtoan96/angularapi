import {NgModule} from '@angular/core';
import {FormsModule} from "@angular/forms";
import {AdministrationComponent} from "./components/Administration.component";
import {AdministrationSettingComponent} from "./components/AdministrationSetting.component";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";

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
        GlobalComponentsModule
    ],
    providers: [
    ]
})
export class AdministrationModule {
}
