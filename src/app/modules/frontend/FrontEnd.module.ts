import {NgModule} from '@angular/core';
import {FEHome} from "./FEHome";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {CommonModule} from "@angular/common";
import {AppRoutingModule} from "../../app-routing.module";
import {FECategory} from "./FECategory";
import {FECategoryID} from "./FECategoryID";
import {FEBlog} from "./FEBlog";

@NgModule({
    declarations: [
        FEHome,
        FECategory,
        FECategoryID,
        FEBlog
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
