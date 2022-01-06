import {NgModule} from '@angular/core';
import {FormsModule} from "@angular/forms";
import {AdministrationComponent} from "./components/Administration.component";

@NgModule({
    declarations: [
        AdministrationComponent
    ],
    exports: [
        AdministrationComponent
    ],
    imports: [
        FormsModule
    ],
    providers: [
    ]
})
export class AdministrationModule {
}
