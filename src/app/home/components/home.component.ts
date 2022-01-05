import {Component, OnInit} from '@angular/core';
import {EventObj} from "@tinymce/tinymce-angular/editor/Events";
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";

@Component({
    selector: 'HomeComponent',
    templateUrl: '../templates/home.html'
})
export class HomeComponent implements OnInit {

    constructor(private session: Session, private backend: Backend) {
    }

    ngOnInit(): void {
        this.backend.getRequest('module/Contacts', {
            limit: 50,
            searchterm: '',
            offset: 0
        }, true).subscribe(res => console.log(res));
    }

    showContent($event: EventObj<any>) {
        console.log($event);
    }
}
