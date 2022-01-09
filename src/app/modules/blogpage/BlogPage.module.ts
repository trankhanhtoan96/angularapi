import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";
import {BlogPageListComponent} from "./components/BlogPageList.component";
import {BlogPageEditComponent} from "./components/BlogPageEdit.component";

@NgModule({
    declarations: [
        BlogPageListComponent,
        BlogPageEditComponent
    ],
    exports: [
        BlogPageListComponent,
        BlogPageEditComponent
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
export class BlogPageModule {
}
