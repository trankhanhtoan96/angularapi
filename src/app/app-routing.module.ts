import {NgModule} from "@angular/core";
import {Routes, RouterModule} from "@angular/router";
import {LoginComponent} from "./modules/users/components/login.component";
import {AdministrationComponent} from "./modules/administration/components/Administration.component";
import {NotFoundComponent} from "./globalcomponents/components/NotFound.component";
import {UserEditComponent} from "./modules/users/components/UserEdit.component";
import {LoginCheck} from "./services/Session.service";
import {UserListComponent} from "./modules/users/components/UserList.component";
import {BlogCategoryListComponent} from "./modules/blogcategory/components/BlogCategoryList.component";
import {BlogCategoryEditComponent} from "./modules/blogcategory/components/BlogCategoryEdit.component";
import {BlogListComponent} from "./modules/blog/components/BlogList.component";
import {BlogEditComponent} from "./modules/blog/components/BlogEdit.component";
import {BlogPageListComponent} from "./modules/blogpage/components/BlogPageList.component";
import {BlogPageEditComponent} from "./modules/blogpage/components/BlogPageEdit.component";
import {BlogCommentListComponent} from "./modules/blogcomment/components/BlogCommentList.component";
import {BlogCommentEditComponent} from "./modules/blogcomment/components/BlogCommentEdit.component";
import {AdministrationSettingComponent} from "./modules/administration/components/AdministrationSetting.component";
import {FEHome} from "./modules/frontend/FEHome";
import {FECategory} from "./modules/frontend/FECategory";

const routes: Routes = [
    {path: 'login', component: LoginComponent},

    {path: 'admin', component: AdministrationComponent, canActivate: [LoginCheck]},
    {path: 'adminsetting', component: AdministrationSettingComponent, canActivate: [LoginCheck]},

    {path: 'admin/users', component: UserListComponent, canActivate: [LoginCheck]},
    {path: 'admin/users/:id', component: UserEditComponent, canActivate: [LoginCheck]},

    {path: 'admin/blogcategory', component: BlogCategoryListComponent, canActivate: [LoginCheck]},
    {path: 'admin/blogcategory/:id', component: BlogCategoryEditComponent, canActivate: [LoginCheck]},

    {path: 'admin/blogpage', component: BlogPageListComponent, canActivate: [LoginCheck]},
    {path: 'admin/blogpage/:id', component: BlogPageEditComponent, canActivate: [LoginCheck]},

    {path: 'admin/blog', component: BlogListComponent, canActivate: [LoginCheck]},
    {path: 'admin/blog/:id', component: BlogEditComponent, canActivate: [LoginCheck]},

    {path: 'admin/blogcomment', component: BlogCommentListComponent, canActivate: [LoginCheck]},
    {path: 'admin/blogcomment/:id', component: BlogCommentEditComponent, canActivate: [LoginCheck]},

    {path: '', component: FEHome},
    {path: 'category/:slug', component: FECategory},
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
