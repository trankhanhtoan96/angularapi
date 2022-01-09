import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {BlogCategoryListComponent} from "./components/BlogCategoryList.component";
import {SystemModule} from "../../system/System.module";
import {BlogCategoryEditComponent} from "./components/BlogCategoryEdit.component";
import {CommonModule} from "@angular/common";

@NgModule({
    declarations: [
        BlogCategoryListComponent,
        BlogCategoryEditComponent
    ],
    exports: [
        BlogCategoryListComponent,
        BlogCategoryEditComponent
    ],
    imports: [
        AppRoutingModule,
        FormsModule,
        GlobalComponentsModule,
        SystemModule,
        CommonModule
    ],
    providers: [
    ]
})
export class BlogCategoryModule {
}
