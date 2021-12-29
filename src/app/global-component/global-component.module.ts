import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FieldSearchComponent } from './field-search/field-search.component';



@NgModule({
    declarations: [
        FieldSearchComponent
    ],
    exports: [
        FieldSearchComponent
    ],
    imports: [
        CommonModule
    ]
})
export class GlobalComponentModule { }
