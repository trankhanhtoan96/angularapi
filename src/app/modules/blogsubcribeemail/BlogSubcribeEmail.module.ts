import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";
import {BlogSubcribeEmailListComponent} from "./components/BlogSubcribeEmailList.component";
import {BlogSubcribeEmailEditComponent} from "./components/BlogSubcribeEmailEdit.component";

@NgModule({
    declarations: [
        BlogSubcribeEmailListComponent,
        BlogSubcribeEmailEditComponent
    ],
    exports: [
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
export class BlogSubcribeEmailModule {
}
