import {NgModule} from '@angular/core';
import {LoginComponent} from "./components/login.component";
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";

@NgModule({
    declarations: [
        LoginComponent
    ],
    exports: [
        LoginComponent
    ],
    imports: [
        AppRoutingModule,
        FormsModule
    ],
    providers: [
    ]
})
export class UsersModule {
}
