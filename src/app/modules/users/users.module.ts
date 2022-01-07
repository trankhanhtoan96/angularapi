import {NgModule} from '@angular/core';
import {LoginComponent} from "./components/login.component";
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {UserEditComponent} from "./components/UserEdit.component";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";

@NgModule({
    declarations: [
        LoginComponent,
        UserEditComponent
    ],
    exports: [
        LoginComponent,
        UserEditComponent
    ],
    imports: [
        AppRoutingModule,
        FormsModule,
        GlobalComponentsModule
    ],
    providers: [
    ]
})
export class UsersModule {
}
