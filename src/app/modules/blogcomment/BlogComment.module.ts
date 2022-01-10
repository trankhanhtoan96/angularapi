import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";
import {BlogCommentEditComponent} from "./components/BlogCommentEdit.component";
import {BlogCommentListComponent} from "./components/BlogCommentList.component";

@NgModule({
    declarations: [
        BlogCommentListComponent,
        BlogCommentEditComponent
    ],
    exports: [
        BlogCommentListComponent,
        BlogCommentEditComponent
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
export class BlogComment {
}
