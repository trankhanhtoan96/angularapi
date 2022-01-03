import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HomeComponent} from "./components/home.component";
import {EditorModule} from "@tinymce/tinymce-angular";
import {GlobalComponentsModule} from "../globalcomponents/GlobalComponents.module";

@NgModule({
    declarations: [
        HomeComponent
    ],
    exports: [
        HomeComponent
    ],
    imports: [
        CommonModule,
        EditorModule,
        GlobalComponentsModule
    ]
})
export class HomeModule {
}
