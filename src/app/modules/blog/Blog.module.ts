import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";
import {BlogListComponent} from "./components/BlogList.component";
import {BlogEditComponent} from "./components/BlogEdit.component";

@NgModule({
    declarations: [
        BlogListComponent,
        BlogEditComponent
    ],
    exports: [
        BlogListComponent,
        BlogEditComponent
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
export class BlogModule {
}
