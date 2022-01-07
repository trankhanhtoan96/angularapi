import {NgModule} from "@angular/core";
import {Routes, RouterModule} from "@angular/router";
import {LoginComponent} from "./modules/users/components/login.component";
import {AdministrationComponent} from "./modules/administration/components/Administration.component";
import {NotFoundComponent} from "./globalcomponents/components/NotFound.component";
import {UserEditComponent} from "./modules/users/components/UserEdit.component";
import {LoginCheck} from "./services/Session.service";

const routes: Routes = [
    {path: 'login', component: LoginComponent},
    {path: 'admin', component: AdministrationComponent, canActivate: [LoginCheck]},
    {path: 'admin/user/:id', component: UserEditComponent, canActivate: [LoginCheck]},
    {path: '', component: NotFoundComponent},
    {path: "**", component: NotFoundComponent}
];

@NgModule({
    imports: [RouterModule.forRoot(routes, {
        initialNavigation: 'enabled'
    })],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
