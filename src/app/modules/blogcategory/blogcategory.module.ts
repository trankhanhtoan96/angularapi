import {NgModule} from '@angular/core';
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";

@NgModule({
    declarations: [
    ],
    exports: [
    ],
    imports: [
        AppRoutingModule,
        FormsModule,
        GlobalComponentsModule
    ],
    providers: [
    ]
})
export class BlogCategoryModule {
}
