import {NgModule} from '@angular/core';
import {FEHome} from "./FEHome";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {CommonModule} from "@angular/common";
import {AppRoutingModule} from "../../app-routing.module";

@NgModule({
    declarations: [
        FEHome
    ],
    exports: [],
    imports: [
        GlobalComponentsModule,
        CommonModule,
        AppRoutingModule
    ],
    providers: []
})
export class FrontEndModule {
}
