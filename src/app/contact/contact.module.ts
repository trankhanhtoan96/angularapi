import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContactComponent } from './contact/contact.component';
import {AppRoutingModule} from "../app-routing.module";
import {GlobalComponentModule} from "../global-component/global-component.module";



@NgModule({
  declarations: [
    ContactComponent
  ],
    imports: [
        CommonModule,
        AppRoutingModule,
        GlobalComponentModule
    ]
})
export class ContactModule { }
