import {NgModule} from '@angular/core';
import {FEHome} from "./FEHome";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";

@NgModule({
    declarations: [
        FEHome
    ],
    exports: [],
    imports: [
        GlobalComponentsModule
    ],
    providers: []
})
export class FrontEndModule {
}
