import {NgModule} from '@angular/core';
import {LoginComponent} from "./components/login.component";
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {UserEditComponent} from "./components/UserEdit.component";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {UserListComponent} from "./components/UserList.component";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";

@NgModule({
    declarations: [
        LoginComponent,
        UserEditComponent,
        UserListComponent
    ],
    exports: [
        LoginComponent,
        UserEditComponent,
        UserListComponent
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
export class UsersModule {
}
