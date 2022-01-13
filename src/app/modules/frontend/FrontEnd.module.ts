import {NgModule} from '@angular/core';
import {FEHome} from "./FEHome";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {CommonModule} from "@angular/common";
import {AppRoutingModule} from "../../app-routing.module";
import {FECategory} from "./FECategory";
import {FECategoryID} from "./FECategoryID";
import {FEBlog} from "./FEBlog";
import {FEBlogID} from "./FEBlogID";
import {FEProfile} from "./FEProfile";
import {FEProfileSave} from "./FEProfileSave";

@NgModule({
    declarations: [
        FEHome,
        FECategory,
        FECategoryID,
        FEBlog,
        FEBlogID,
        FEProfile,
        FEProfileSave
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
