import {NgModule} from "@angular/core";
import {Routes, RouterModule} from "@angular/router";
import {HomeComponent} from "./home/components/home.component";
import {LoginComponent} from "./modules/users/components/login.component";
import {AdministrationComponent} from "./modules/administration/components/Administration.component";

const routes: Routes = [
    {path: 'login', component: LoginComponent},
    {path: 'admin', component: AdministrationComponent},
    {path: "**", component: HomeComponent}
];

@NgModule({
    imports: [RouterModule.forRoot(routes, {
        initialNavigation: 'enabled'
    })],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
