import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";
import {Backend} from "../../services/Backend.service";

@Component({
    selector: 'AdminMenuComponent',
    templateUrl: '../templates/AdminMenu.html'
})
export class AdminMenuComponent implements OnInit {

    constructor(public session: Session, public backend: Backend) {
    }

    ngOnInit(): void {

    }
}
