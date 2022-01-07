import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HeaderComponent} from "./components/header.component";
import {AdsTopComponent} from "./components/adstop.component";
import {FormFieldEditorComponent} from "./components/FormFieldEditor.component";
import {EditorModule, TINYMCE_SCRIPT_SRC} from '@tinymce/tinymce-angular';
import {ToastComponent} from "./components/toast.component";
import {ToastContainerModule, ToastNoAnimationModule} from "ngx-toastr";
import {FormsModule} from "@angular/forms";
import {AdminMenuComponent} from "./components/AdminMenu.component";
import {RouterModule} from "@angular/router";
import {FooterComponent} from "./components/Footer.component";
import {SpinnerDottedModule} from "spinners-angular/spinner-dotted";
import {BackdropSmallComponent} from "./components/BackdropSmall.component";
import {BackdropLargeComponent} from "./components/BackdropLarge.component";
import {BackdropComponent} from "./components/Backdrop.component";
import {NotFoundComponent} from "./components/NotFound.component";
import {FormFieldTextComponent} from "./components/FormFieldText.component";
import {FormFormComponent} from "./components/FormForm.component";


@NgModule({
    declarations: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent,
        ToastComponent,
        AdminMenuComponent,
        FooterComponent,
        BackdropSmallComponent,
        BackdropLargeComponent,
        BackdropComponent,
        NotFoundComponent,
        FormFieldTextComponent,
        FormFormComponent
    ],
    exports: [
        HeaderComponent,
        AdsTopComponent,
        FormFieldEditorComponent,
        ToastComponent,
        AdminMenuComponent,
        FooterComponent,
        BackdropSmallComponent,
        BackdropLargeComponent,
        BackdropComponent,
        NotFoundComponent,
        FormFieldTextComponent,
        FormFormComponent
    ],
    imports: [
        CommonModule,
        EditorModule,
        ToastContainerModule,
        ToastNoAnimationModule.forRoot({
            timeOut: 5000,
            positionClass: 'toast-top-right'
        }),
        FormsModule,
        RouterModule,
        SpinnerDottedModule,
    ],
    providers: [
        {provide: TINYMCE_SCRIPT_SRC, useValue: 'tinymce/tinymce.min.js'},
        ToastComponent
    ]
})
export class GlobalComponentsModule {
}
