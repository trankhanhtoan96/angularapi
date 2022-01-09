import {NgModule} from '@angular/core';
import {FormsModule} from "@angular/forms";
import {RouterModule} from "@angular/router";
import {GlobalComponentsModule} from "../globalcomponents/GlobalComponents.module";
import {ListViewComponent} from "./components/ListView.component";
import {EditViewComponent} from "./components/EditView.component";

@NgModule({
    declarations: [
        ListViewComponent,
        EditViewComponent
    ],
    exports: [
        ListViewComponent,
        EditViewComponent
    ],
    imports: [
        FormsModule,
        GlobalComponentsModule,
        RouterModule
    ],
    providers: [
    ]
})
export class SystemModule {
}
