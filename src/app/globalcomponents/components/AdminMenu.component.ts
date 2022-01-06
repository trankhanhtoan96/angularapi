import {Component, OnInit} from '@angular/core';
import {Session} from "../../services/Session.service";

@Component({
    selector: 'AdminMenuComponent',
    templateUrl: '../templates/AdminMenu.html'
})
export class AdminMenuComponent implements OnInit {

    constructor(public session: Session) {
    }

    ngOnInit(): void {

    }
}
