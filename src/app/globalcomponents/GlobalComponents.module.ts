import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HeaderComponent} from "./components/header.component";
import {AdsTopComponent} from "./components/adstop.component";
import {FormFieldEditorComponent} from "./components/FormFieldEditor.component";
import {EditorModule, TINYMCE_SCRIPT_SRC} from '@tinymce/tinymce-angular';
import {ToastComponent} from "./components/toast.component";
import {ToastContainerModule, ToastNoAnimationModule} from "ngx-toastr";


@NgModule({
    declarations: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent,
        ToastComponent
    ],
    exports: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent
    ],
    imports: [
        CommonModule,
        EditorModule,
        ToastContainerModule,
        ToastNoAnimationModule.forRoot({
            timeOut: 10000,
            positionClass: 'toast-top-right'
        }),
    ],
    providers: [
        {provide: TINYMCE_SCRIPT_SRC, useValue: 'tinymce/tinymce.min.js'}
    ]
})
export class GlobalComponentsModule {
}
