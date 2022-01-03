import {Component, OnInit} from '@angular/core';
import {EventObj} from "@tinymce/tinymce-angular/editor/Events";

@Component({
    selector: 'HomeComponent',
    templateUrl: '../templates/home.html'
})
export class HomeComponent implements OnInit {

    constructor() {
    }

    ngOnInit(): void {

    }

    showContent($event: EventObj<any>) {
        console.log($event);
    }
}
