import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HeaderComponent} from "./components/header.component";
import {AdsTopComponent} from "./components/adstop.component";
import {FormFieldEditorComponent} from "./components/FormFieldEditor.component";
import {EditorModule} from "@tinymce/tinymce-angular";


@NgModule({
    declarations: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent
    ],
    exports: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent
    ],
    imports: [
        CommonModule,
        EditorModule
    ]
})
export class GlobalComponentsModule {
}
